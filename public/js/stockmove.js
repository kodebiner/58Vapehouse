/**
 * stockmove.js - Consolidated Stock Movement JS
 * Uses event delegation and data attributes.
 */
var StockMoveApp = (function($) {
    'use strict';

    var createVariantData = null;
    var editVariantData = {};

    // ============================================================
    //  Utilities
    // ============================================================

    function el(tag, cls) {
        var e = document.createElement(tag);
        if (cls) e.setAttribute('class', cls);
        return e;
    }

    function findVariant(arr, id) {
        for (var i = 0; i < arr.length; i++) {
            if (String(arr[i].id) === String(id)) return arr[i];
        }
        return null;
    }

    // ============================================================
    //  Create Modal
    // ============================================================

    function initCreateModal() {
        if (!window.productList) return;

        $('#productname').autocomplete({
            source: window.productList,
            select: function(e, i) {
                $.ajax({
                    url: 'stockmove/product',
                    method: 'POST',
                    data: { id: i.item.idx },
                    dataType: 'json',
                    success: function(data) {
                        createVariantData = data;
                        renderVariantGrid(data, 'tablevariant', 'variantlist', 'create');
                    }
                });
            },
            minLength: 2
        });

        $(document).on('click', '.js-add-variant-create', function() {
            var varId = $(this).data('var-id');
            createAddVariant(varId);
        });
    }

    function createAddVariant(varId) {
        if (!createVariantData) return;
        var v = findVariant(createVariantData, varId);
        if (!v) return;
        if (v.qty === '0' || v.qty === 0) { alert(window.alertStock); return; }
        if ($('#tableproduct').find('[data-var-id="' + v.id + '"]').length) { alert(window.alertReadyAdd); return; }
        addProductRow(v, 'tableproduct', '', 'totalpcs');
        $('#productname').val('');
    }

    // ============================================================
    //  Edit Modal
    // ============================================================

    function initEditModals() {
        $('.js-edit-product').each(function() {
            var $input = $(this);
            var smId = $input.data('sm-id');

            $input.autocomplete({
                source: window.productList,
                select: function(e, i) {
                    var originVal = $input.closest('form').find('select[name="origin"]').val();
                    $.ajax({
                        url: 'stockmove/product',
                        method: 'POST',
                        data: { id: i.item.idx, outletid: originVal },
                        dataType: 'json',
                        success: function(data) {
                            editVariantData[smId] = data;
                            renderVariantGrid(data, 'tabvar' + smId, 'variantliste', 'edit_' + smId);
                        }
                    });
                },
                minLength: 2
            });
        });

        $(document).on('click', '.js-add-variant-edit', function() {
            var smId = $(this).data('sm-id');
            var varId = $(this).data('var-id');
            editAddVariant(smId, varId);
        });
    }

    function editAddVariant(smId, varId) {
        var variants = editVariantData[smId];
        if (!variants) return;
        var v = findVariant(variants, varId);
        if (!v) return;
        if ($('#editdata' + smId).find('[data-var-id="' + varId + '"]:not(.js-add-variant-edit)').length) {
            alert(window.alertReadyAdd);
            return;
        }
        addProductRow(v, 'tableprod' + smId, 'e', 'addtotalpcs');
        $('#prodname' + smId).val('');
    }

    // ============================================================
    //  Shared: Render Variant Grid
    // ============================================================

    function renderVariantGrid(variants, containerId, listId, mode) {
        var container = document.getElementById(containerId);
        if (!container) return;
        container.removeAttribute('hidden');

        var existing = document.getElementById(listId);
        if (existing) existing.remove();

        var grid = el('div', 'uk-padding uk-padding-remove-vertical');
        grid.setAttribute('id', listId);
        grid.setAttribute('uk-grid', '');

        for (var i = 0; i < variants.length; i++) {
            var v = variants[i];

            var c1 = el('div', 'uk-flex uk-flex-middle uk-width-1-4 uk-margin-small');
            c1.innerHTML = v.name;

            var c2 = el('div', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4 uk-margin-small');
            c2.innerHTML = v.qty;

            var c3 = el('div', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4 uk-margin-small');
            c3.innerHTML = v.wholesale;

            var c4 = el('div', 'uk-flex uk-flex-center uk-flex-middle uk-width-1-4 uk-margin-small');
            var btn = document.createElement('a');
            btn.setAttribute('class', 'uk-icon-button');
            btn.setAttribute('uk-icon', 'cart');

            if (mode === 'create') {
                btn.setAttribute('data-var-id', v.id);
                btn.setAttribute('class', 'uk-icon-button js-add-variant-create');
            } else if (mode.indexOf('edit_') === 0) {
                var smId = parseInt(mode.split('_')[1]);
                btn.setAttribute('data-sm-id', smId);
                btn.setAttribute('data-var-id', v.id);
                btn.setAttribute('class', 'uk-icon-button js-add-variant-edit');
            }

            c4.appendChild(btn);
            grid.appendChild(c1);
            grid.appendChild(c2);
            grid.appendChild(c3);
            grid.appendChild(c4);
        }

        container.appendChild(grid);
    }

    // ============================================================
    //  Shared: Add Product Row
    // ============================================================

    function addProductRow(variant, tableId, prefix, namePrefix) {
        var prods = document.getElementById(tableId);
        if (!prods) return;

        var rowId = prefix ? 'eproduct' + variant.id : 'product' + variant.id;
        if (document.getElementById(rowId)) return;

        var grid = el('div', 'uk-margin-small uk-flex-middle uk-flex-center');
        grid.setAttribute('id', rowId);
        grid.setAttribute('data-var-id', variant.id);
        grid.setAttribute('uk-grid', '');

        // Variant name
        var vc = el('div', 'uk-width-1-6');
        var vn = document.createElement('div');
        vn.innerHTML = variant.name;
        vc.appendChild(vn);

        // Qty controls
        var tc = el('div', 'uk-width-1-2 uk-text-center');

        var delBtn = el('div', 'tm-h2 pointerbutton uk-button uk-button-small uk-button-danger');
        delBtn.innerHTML = '-';

        var inp = document.createElement('input');
        inp.setAttribute('type', 'number');
        inp.setAttribute('id', namePrefix + '[' + variant.id + ']');
        inp.setAttribute('name', namePrefix + '[' + variant.id + ']');
        inp.setAttribute('max', variant.qty);
        inp.setAttribute('class', 'uk-input uk-width-1-3');
        inp.setAttribute('value', '1');
        inp.setAttribute('required', '');

        var addBtn = el('div', 'tm-h2 pointerbutton uk-button uk-button-small uk-button-primary');
        addBtn.innerHTML = '+';

        tc.appendChild(delBtn);
        tc.appendChild(inp);
        tc.appendChild(addBtn);

        // Price
        var pc = el('div', 'uk-width-1-6 uk-text-center');
        var pi = document.createElement('input');
        pi.setAttribute('type', 'number');
        pi.setAttribute('hidden', '');
        pi.setAttribute('id', (prefix || 'b') + 'price[' + variant.id + ']');
        pi.setAttribute('class', 'uk-input');
        pi.setAttribute('value', variant.wholesale);
        var pd = document.createElement('div');
        pd.innerHTML = variant.wholesale;
        pc.appendChild(pi);
        pc.appendChild(pd);

        // Subtotal
        var sc = el('div', 'uk-width-1-6 uk-text-center');
        var st = document.createElement('div');
        st.setAttribute('id', (prefix || '') + 'subtotal' + variant.id);
        st.setAttribute('class', 'subvariant');
        st.innerHTML = variant.wholesale;
        sc.appendChild(st);

        grid.appendChild(vc);
        grid.appendChild(tc);
        grid.appendChild(pc);
        grid.appendChild(sc);
        prods.appendChild(grid);

        // Event handlers
        function recalc() {
            var p = parseFloat(pi.value) || 0;
            var q = parseInt(inp.value) || 0;
            st.innerHTML = p * q;
            st.setAttribute('value', p * q);
            if (inp.value == '0') { grid.remove(); }
            updateFinalTotal(tableId);
        }

        inp.addEventListener('change', function() {
            var max = parseInt(this.getAttribute('max'));
            var val = parseInt(this.value);
            if (val > max) { this.value = max; alert(window.alertStock); }
            recalc();
        });

        delBtn.addEventListener('click', function() {
            var val = parseInt(inp.value) || 0;
            if (val <= 1) { inp.value = '0'; grid.remove(); }
            else { inp.value = val - 1; }
            recalc();
        });

        addBtn.addEventListener('click', function() {
            var max = parseInt(inp.getAttribute('max'));
            var val = parseInt(inp.value) || 0;
            if (val >= max) { inp.value = max; alert(window.alertStock); }
            else { inp.value = val + 1; }
            recalc();
        });

        recalc();
    }

    // ============================================================
    //  Edit Modal: Existing Item Controls
    // ============================================================

    function initEditExistingControls() {
        $(document).on('click', '.js-edit-existing-minus', function() {
            adjustQty($(this), -1);
        });

        $(document).on('click', '.js-edit-existing-plus', function() {
            adjustQty($(this), 1);
        });

        $(document).on('change', '.js-edit-existing-qty', function() {
            var $input = $(this);
            var max = parseInt($input.attr('max'));
            var min = parseInt($input.attr('min')) || 0;
            var val = parseInt($input.val());
            if (isNaN(val)) val = min;
            if (val > max) { $input.val(max); alert(window.alertStock); }
            if (val <= min) { $input.val('0'); $input.closest('[uk-grid]').remove(); }
            recalcEditRow($input);
        });

        $(document).on('change', '.js-edit-existing-price', function() {
            var $input = $(this);
            recalcEditRow($input);
        });
    }

    function adjustQty($btn, delta) {
        var $grid = $btn.closest('[uk-grid]');
        var $qty = $grid.find('.js-edit-existing-qty');
        var max = parseInt($qty.attr('max'));
        var val = parseInt($qty.val()) || 0;
        var newVal = val + delta;
        if (newVal > max) { newVal = max; alert(window.alertStock); }
        if (newVal <= 0) { $qty.val('0'); $grid.remove(); return; }
        $qty.val(newVal);
        recalcEditRow($qty);
    }

    function recalcEditRow($qtyInput) {
        var $grid = $qtyInput.closest('[uk-grid]');
        var qty = parseInt($qtyInput.val()) || 0;
        var price = parseFloat($grid.find('.js-edit-existing-price').val()) || 0;
        var $sub = $grid.find('.js-edit-subtotal');
        var total = qty * price;
        $sub.html(total).attr('value', total);

        // Update final total for this edit modal
        editUpdateFinalTotal();
    }

    function editUpdateFinalTotal() {
        // Recalculate totals for all edit modals
        $('[id^="tableprod"]').each(function() {
            var id = $(this).attr('id');
            var total = 0;
            $(this).find('.subvariant').each(function() {
                total += parseFloat($(this).html()) || 0;
            });
            // No final price display for edit modal currently - add if needed
        });
    }

    // ============================================================
    //  Confirm Modal
    // ============================================================

    function initConfirmModals() {
        $(document).on('change', '.js-confirm-qty', function() {
            var $input = $(this);
            var smId = $input.data('sm-id');
            var varId = $input.data('var-id');
            var price = parseFloat($input.data('price')) || 0;
            var qty = parseInt($input.val()) || 0;

            if (qty === 0) {
                $input.closest('tr').remove();
                updateConfirmTotal(smId);
                return;
            }

            var $row = $input.closest('tr');
            $row.find('.js-confirm-subtotal').html(price * qty);
            updateConfirmTotal(smId);
        });
    }

    function updateConfirmTotal(smId) {
        var total = 0;
        $('.js-confirm-subtotal[data-sm-id="' + smId + '"]').each(function() {
            total += parseFloat($(this).html()) || 0;
        });
        $('#cfinalprice' + smId).html('Rp. ' + total + ',-');
    }

    // ============================================================
    //  Final Total (Create Modal)
    // ============================================================

    function updateFinalTotal(tableId) {
        var subTotal = 0;
        $('#' + tableId + ' .subvariant').each(function() {
            subTotal += parseFloat($(this).html()) || 0;
        });
        $('#finalprice').html('Rp. ' + subTotal + ',-');
    }

    // ============================================================
    //  Public API
    // ============================================================

    return {
        init: function() {
            if (window.productList) {
                initCreateModal();
                initEditModals();
                initEditExistingControls();
                initConfirmModals();
            }
        }
    };

})(jQuery);

$(function() {
    StockMoveApp.init();
});
