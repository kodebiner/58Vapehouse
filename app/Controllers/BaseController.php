<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\UserModel;
use Myth\Auth\Models\GroupModel;
use App\Models\GroupUserModel;
use App\Models\GconfigModel;
use App\Models\OutletModel;
use App\Models\OutletaccessModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['auth'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        $session = \Config\Services::session();
        $this->agent = $this->request->getUserAgent();
        $this->locale = service('request')->getLocale();
        $this->uri = $this->request->uri;

        // Calling Model
        $this->userModel = new UserModel();
        $this->GroupModel = new GroupModel();
        $this->GroupUserModel = new GroupUserModel();
        $this->ConfigModel = new GconfigModel();
        $this->OutletModel = new OutletModel();
        $this->OutletAccessModel = new OutletaccessModel();

        // Login Check
        $auth = service('authentication');
        if (!$auth->check()) {
            $this->userId = null;
            $fullname = '';
            $this->user = null;
            $outletUser = '';
        }
        else {
            $this->userId = $auth->id();
            $this->user = $this->userModel->find($this->userId);
            $fullname = $this->user->getname();
            
            // Getting User Role
            $GroupUser = $this->GroupUserModel->where('user_id', $this->userId)->first();
            $role = $this->GroupModel->find($GroupUser['group_id']);

            // Get User Outlet Access
            $outletUser = $this->OutletAccessModel->where('userid',$this->userId)->find();
        }

        // Language check
		if ($this->locale === 'id') {
			$lang = 'id';
		} else {
			$lang = 'en';
		}

        // Load Config
        $this->gconfig = $this->ConfigModel->first();

        if (!empty($this->gconfig)) {
            $gconfig = $this->gconfig;
        } else {
            $gconfig = [
                'id'                => null,
                'poinvalue'         => null,
                'poinorder'         => null,
                'memberdisc'        => null,
                'memberdisctype'    => null,
                'logo'              => null,
                'bizname'           => null,
                'ppn'               => null,
            ];
        }

        // Determining outlet sessionn
        $outletSes = $session->get('outlet');
        if ($outletSes != null) {
            $outletPick = $outletSes;
        } else {
            $outletPick = null;
        }
        

        // Parsing View Data
        $this->data = [
			'ismobile'	    => $this->agent->isMobile(),
            'lang'          => $lang,
			'uri'		    => $this->uri,
            'uid'           => $this->userId,
            'authorize'     => service('authorization'),
            'account'       => $this->user,
            'fullname'      => $fullname,
            'outlets'       => $this->OutletModel->findAll(),
            'baseoutlets'   => $outletUser,
            'gconfig'       => $gconfig,
            'outletPick'    => $outletPick,
		];

        if ($auth->check()) {
            $this->data['role'] = $role->name;
        }
    }
}
