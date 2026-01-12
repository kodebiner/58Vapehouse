<?php

namespace App\Libraries;

use App\Models\{
    VariantModel,
    BundleModel,
    MemberModel
};

class PriceCalculator
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function calculate(array $input): array
    {
        $VariantModel = new VariantModel();
        $BundleModel  = new BundleModel();

        $memberid = $input['customerid'] ?? null;

        $varvalues = [];
        $variants  = [];

        // ================= VARIANT =================
        foreach ($input['qty'] ?? [] as $varId => $qty) {
            if ($qty <= 0) continue;

            $variant = $VariantModel->find($varId);
            if (!$variant) continue;

            $discvar = isset($input['varprice'][$varId])
                ? (int)$input['varprice'][$varId] * $qty
                : 0;

            $globaldisc = 0;
            if (!empty($this->config['globaldisc'])) {
                if ($this->config['globaldisctype'] === '0') {
                    $globaldisc = (int)$this->config['globaldisc'] * $qty;
                } else {
                    $globaldisc = ((int)$this->config['globaldisc'] / 100)
                        * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])
                        * $qty;
                }
            }

            $memberdisc = 0;
            if ($memberid) {
                if ($this->config['memberdisctype'] === '0') {
                    $memberdisc = $this->config['memberdisc'] * $qty;
                } else {
                    $memberdisc = ((int)$this->config['memberdisc'] / 100)
                        * ((int)$variant['hargamodal'] + (int)$variant['hargajual'])
                        * $qty;
                }
                $max = $this->config['maxmemberdisc'] * $qty;
                if ($memberdisc > $max) $memberdisc = $max;
            }

            $price = ((int)$variant['hargamodal'] + (int)$variant['hargajual'])
                - ($discvar / max(1, $qty))
                - ($globaldisc / max(1, $qty))
                - ($memberdisc / max(1, $qty));
                
            $price = max(0, $price);

            $varvalues[] = $price * $qty;

            $variants[] = [
                'id'            => $varId,
                'qty'           => $qty,
                'value'         => $price,
                'discvar'       => $discvar,
                'globaldisc'    => $globaldisc,
                'memberdisc'    => $memberdisc,
                'marginmodal'   => $price - (int)$variant['hargamodal'],
                'margindasar'   => $price - (int)$variant['hargadasar'],
            ];
        }

        // ================= BUNDLE =================
        $bundvalues = [];
        $bundles    = [];

        foreach ($input['bqty'] ?? [] as $bunId => $qty) {
            if ($qty <= 0) continue;

            $bundle = $BundleModel->find($bunId);
            if (!$bundle) continue;

            $price = (int)$bundle['price'];
            $globaldisc = 0;

            if (!empty($this->config['globaldisc'])) {
                if ($this->config['globaldisctype'] === '0') {
                    $globaldisc = (int)$this->config['globaldisc'] * $qty;
                } else {
                    $globaldisc = ((int)$this->config['globaldisc'] / 100)
                        * $price * $qty;
                }
            }

            $memberdisc = 0;
            if ($memberid) {
                if ($this->config['memberdisctype'] === '0') {
                    $memberdisc = $this->config['memberdisc'] * $qty;
                } else {
                    $memberdisc = ((int)$this->config['memberdisc'] / 100)
                        * $price * $qty;
                }
                $max = $this->config['maxmemberdisc'] * $qty;
                if ($memberdisc > $max) $memberdisc = $max;
            }

            $final = $price - ($globaldisc / max(1, $qty)) - ($memberdisc / max(1, $qty));
            $final = max(0, $final);

            $bundvalues[] = $final * $qty;

            $bundles[] = [
                'id'         => $bunId,
                'qty'        => $qty,
                'value'      => $final,
                'globaldisc' => $globaldisc,
                'memberdisc' => $memberdisc,
            ];
        }

        $subtotal = array_sum($varvalues) + array_sum($bundvalues);

        // ================= TRANSACTION DISC =================
        $discount = 0;
        if (!empty($input['discvalue'])) {
            $discount = $input['disctype'] === '1'
                ? ((int)$input['discvalue'] / 100) * $subtotal
                : (int)$input['discvalue'];
        }

        if (!empty($input['poin'])) {
            $member = (new MemberModel())->find($input['customerid']);
            if ($input['poin'] > $member['poin']) {
                throw new \Exception('Poin tidak mencukupi');
            }
        }

        $poin = (int)($input['poin'] ?? 0);
        $value = $subtotal - $discount - $poin;

        $ppn = (int)$value * ((int)$this->config['ppn'] / 100);
        $total = max(0, $value + $ppn);

        return compact(
            'variants',
            'bundles',
            'subtotal',
            'discount',
            'poin',
            'ppn',
            'total'
        );
    }
}