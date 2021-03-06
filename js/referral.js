(function($) {
    "use strict";
    $.storage = new $.store();
    $.referral = {
        options: {},
        // last list view user has visited: {title: "...", hash: "..."}
        lastView: null,
        init: function(options) {
            var that = this;
            that.options = options;
            if (typeof ($.History) != "undefined") {
                $.History.bind(function() {
                    that.dispatch();
                });
            }
            $.wa.errorHandler = function(xhr) {
                if ((xhr.status === 403) || (xhr.status === 404)) {
                    var text = $(xhr.responseText);
                    if (text.find('.dialog-content').length) {
                        text = $('<div class="block double-padded"></div>').append(text.find('.dialog-content *'));

                    } else {
                        text = $('<div class="block double-padded"></div>').append(text.find(':not(style)'));
                    }
                    $("#slider-content").empty().append(text);
                    that.onPageNotFound();
                    return false;
                }
                return true;
            };
            var hash = this.getHash();
            if (hash === '#/' || !hash) {
                this.dispatch();
            } else {
                $.wa.setHash(hash);
            }

        },
        // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
        // *   Dispatch-related
        // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

        // if this is > 0 then this.dispatch() decrements it and ignores a call
        skipDispatch: 0,
        /** Cancel the next n automatic dispatches when window.location.hash changes */
        stopDispatch: function(n) {
            this.skipDispatch = n;
        },
        /** Force reload current hash-based 'page'. */
        redispatch: function() {
            this.currentHash = null;
            this.dispatch();
        },
        /**
         * Called automatically when window.location.hash changes.
         * Call a corresponding handler by concatenating leading non-int parts of hash,
         * e.g. for #/aaa/bbb/111/dd/12/ee/ff
         * a method $.slider.AaaBbbAction('111', 'dd', '12', 'ee', 'ff') will be called.
         */
        dispatch: function(hash) {
            if (this.skipDispatch > 0) {
                this.skipDispatch--;
                return false;
            }

            if (hash === undefined || hash === null) {
                hash = this.getHash();
            }
            if (this.currentHash == hash) {
                return;
            }

            this.currentHash = hash;
            hash = hash.replace('#/', '');

            if (hash) {
                hash = hash.split('/');
                if (hash[0]) {
                    var actionName = "";
                    var attrMarker = hash.length;
                    for (var i = 0; i < hash.length; i++) {
                        var h = hash[i];
                        if (i < 2) {
                            if (i === 0) {
                                actionName = h;
                            } else if (parseInt(h, 10) != h && h.indexOf('=') == -1) {
                                actionName += h.substr(0, 1).toUpperCase() + h.substr(1);
                            } else {
                                attrMarker = i;
                                break;
                            }
                        } else {
                            attrMarker = i;
                            break;
                        }
                    }
                    var attr = hash.slice(attrMarker);
                    this.preExecute(actionName);
                    if (typeof (this[actionName + 'Action']) == 'function') {
                        $.shop.trace('$.referral.dispatch', [actionName + 'Action', attr]);
                        this[actionName + 'Action'].apply(this, attr);
                    } else {
                        $.shop.error('Invalid action name:', actionName + 'Action');
                    }
                    this.postExecute(actionName);
                } else {
                    this.preExecute();
                    this.defaultAction();
                    this.postExecute();
                }
            } else {
                this.preExecute();
                this.defaultAction();
                this.postExecute();
            }


        },
        preExecute: function(actionName, attr) {
        },
        postExecute: function(actionName, attr) {
            this.actionName = actionName;
        },
        defaultAction: function() {
            this.load('?plugin=referral&action=promos', function() {
                $.referral.deletePromosInit();
                $.referral.selectAllPromosInit();
            });
        },
        referralsAction: function() {
            this.load('?plugin=referral&action=referrals', function() {
                $.referral.initReferralsButtons();
            });
        },
        paymentsAction: function() {
            this.load('?plugin=referral&action=payments', function() {
                $.referral.initPaymentsButtons();

            });
        },
        couponsAction: function() {
            this.load('?plugin=referral&action=coupons', function() {

            });
        },
        referralAction: function(id) {
            this.load('?plugin=referral&action=referral&referral_id=' + id, function() {
                $.referral.initReferralButtons(id);
                $.referral.initAddTransactionForm();
                $.referral.initTransferForm();
            });
        },
        selectAllPromosInit: function() {
            $('.select-all-promos').click(function() {
                if ($(this).attr('checked')) {
                    $('.select-promo-checkbox').attr('checked', 'checked');
                } else {
                    $('.select-promo-checkbox').removeAttr('checked');
                }
            });
        },
        initAddTransactionForm: function() {

            $('form.add-transaction-form').submit(function() {
                var $form = $(this);
                $('#response-status').html('<i style="vertical-align:middle" class="icon16 loading"></i>');
                $('#response-status').show();
                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    dataType: 'json',
                    data: $form.serialize(),
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $form.find('#response-status').html('<i style="vertical-align:middle" class="icon16 yes"></i>' + data.response.message);
                            $form.find('#response-status').css('color', '#008727');
                            setTimeout(function() {
                                $form.find('#response-status').hide();
                                location.reload();
                            }, 3000);
                        } else {
                            $form.find('#response-status').html('<i style="vertical-align:middle" class="icon16 no"></i>' + data.errors);
                            $form.find('#response-status').css('color', '#FF0000');
                        }

                    }
                });
                return false;
            });
        },
        initTransferForm: function() {

            $('form.transfer-form').submit(function() {
                var $form = $(this);
                $('#response-status').html('<i style="vertical-align:middle" class="icon16 loading"></i>');
                $('#response-status').show();
                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    dataType: 'json',
                    data: $form.serialize(),
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $form.find('#response-status').html('<i style="vertical-align:middle" class="icon16 yes"></i>Сохранено');
                            $form.find('#response-status').css('color', '#008727');
                            setTimeout(function() {
                                $form.find('#response-status').hide();
                                location.reload();
                            }, 3000);
                        } else {
                            $form.find('#response-status').html('<i style="vertical-align:middle" class="icon16 no"></i>' + data.errors);
                            $form.find('#response-status').css('color', '#FF0000');
                        }

                    }
                });
                return false;
            });
        },
        initPaymentsButtons: function() {
            var _csrf = $('input[name="_csrf"]').val();
            $('#payments-list .edit').click(function() {
                var $tr = $(this).closest('tr');
                $tr.find('.status_val').hide();
                $tr.find('.status_input').show();
                $tr.find('.edit').hide();
                //$tr.find('.delete').hide();
                $tr.find('.cancel').show();
                $tr.find('.save').show();
                return false;
            });
            $('#payments-list .cancel').click(function() {
                var $tr = $(this).closest('tr');
                $tr.find('.status_val').show();
                $tr.find('.status_input').hide();
                $tr.find('.edit').show();
                //$tr.find('.delete').show();
                $tr.find('.cancel').hide();
                $tr.find('.save').hide();
                return false;
            });

            $('#payments-list .save').click(function() {
                var $tr = $(this).closest('tr');
                $tr.find('i.loading').css('display', 'inline-block');
                $tr.find('.cancel').hide();
                $.ajax({
                    type: 'POST',
                    url: '?plugin=referral&action=savePayment',
                    dataType: 'json',
                    data: {
                        id: $tr.data('payment-id'),
                        status: $tr.find('.status_input').val(),
                        _csrf: _csrf
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $tr.find('.status_val').text(data.data.status);
                            $tr.find('.cancel').click();
                        } else {
                            alert(data.errors);
                            $tr.find('.cancel').click();
                        }
                        $tr.find('i.loading').hide();
                    }
                });
                return false;
            });
            $('#payments-list .show-full-description').click(function() {
                var $tr = $(this).closest('tr');
                if ($(this).hasClass('showed')) {
                    $tr.find('.full-description').hide();
                    $tr.find('.briefly-description').show();
                    $(this).text('Показать');
                    $(this).removeClass('showed');
                } else {
                    $tr.find('.briefly-description').hide();
                    $tr.find('.full-description').show();
                    $(this).text('Скрыть');
                    $(this).addClass('showed');
                }
                return false;
            });


        },
        initReferralsButtons: function() {
            $('.select-all-referrals').click(function() {
                if ($(this).attr('checked')) {
                    $('.select-referral-checkbox').attr('checked', 'checked');
                } else {
                    $('.select-referral-checkbox').removeAttr('checked');
                }
            });

            $('.delete-referrals-but').click(function() {
                var $form = $('.referrals-form');
                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serializeArray(),
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR) {
                        $.referral.message(data, {content: $('.del-referrals-result')});
                        if (data.status == 'ok') {
                            $.referral.referralsAction();
                        }
                    },
                    error: function(jqXHR, errorText) {
                    }
                });
            });
        },
        initReferralButtons: function(id) {

            $('.select-all-transactions').click(function() {
                if ($(this).attr('checked')) {
                    $('.select-transaction-checkbox').attr('checked', 'checked');
                } else {
                    $('.select-transaction-checkbox').removeAttr('checked');
                }
            });

            $('.delete-transactions-but').click(function() {
                var $form = $('.transactions-form');
                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serializeArray(),
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR) {
                        $.referral.message(data, {content: $('.del-transactions-result')});
                        if (data.status == 'ok') {
                            $.referral.referralAction(id);
                        }
                    },
                    error: function(jqXHR, errorText) {
                    }
                });
            });

            var _csrf = $('input[name="_csrf"]').val();
            $('#transactions-list .edit').click(function() {
                var $tr = $(this).closest('tr');
                //$tr.find('.location_val').hide();
                //$tr.find('.location_input').show();
                $tr.find('.date_val').hide();
                $tr.find('.date_input').show();
                $tr.find('.comment_val').hide();
                $tr.find('.comment_input').show();
                $tr.find('.amount_val').hide();
                $tr.find('.amount_input').show();
                $tr.find('.edit').hide();
                $tr.find('.delete').hide();
                $tr.find('.cancel').show();
                $tr.find('.save').show();
                return false;
            });
            $('#transactions-list .cancel').click(function() {
                var $tr = $(this).closest('tr');
                //$tr.find('.location_val').show();
                //$tr.find('.location_input').hide();
                $tr.find('.date_val').show();
                $tr.find('.date_input').hide();
                $tr.find('.comment_val').show();
                $tr.find('.comment_input').hide();
                $tr.find('.amount_val').show();
                $tr.find('.amount_input').hide();
                $tr.find('.edit').show();
                $tr.find('.delete').show();
                $tr.find('.cancel').hide();
                $tr.find('.save').hide();
                return false;
            });

            $('#transactions-list .save').click(function() {
                var $tr = $(this).closest('tr');
                $tr.find('i.loading').css('display', 'inline-block');
                $tr.find('.cancel').hide();
                $.ajax({
                    type: 'POST',
                    url: '?plugin=referral&action=saveTransaction',
                    dataType: 'json',
                    data: {
                        id: $tr.attr('data-transaction-id'),
                        //location: $tr.find('.location_input').val(),
                        amount: $tr.find('.amount_input').val(),
                        comment: $tr.find('.comment_input').val(),
                        date: $tr.find('.date_input').val(),
                        _csrf: _csrf
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            //$tr.find('.location_val').text($tr.find('.location_input option:selected').text());
                            $tr.find('.amount_val').html(data.data.amount);
                            $tr.find('.date_val').text(data.data.date);
                            $tr.find('.comment_val').text(data.data.comment);
                            $tr.find('.cancel').click();
                        } else {
                            alert(data.errors);
                            $tr.find('.cancel').click();
                        }
                        $tr.find('i.loading').hide();
                    }
                });
                return false;
            });
            $('#transactions-list .delete').click(function() {
                if (!confirm('Вы уверены')) {
                    return false;
                }
                var $tr = $(this).closest('tr');
                $tr.find('i.loading').css('display', 'inline-block');
                $tr.find('.save').hide();
                $.ajax({
                    type: 'POST',
                    url: '?plugin=referral&action=deleteTransaction',
                    dataType: 'json',
                    data: {
                        id: $tr.attr('data-transaction-id'),
                        _csrf: _csrf
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $tr.remove();
                        } else {
                            alert(data.errors);
                            $tr.find('.cancel').click();
                        }
                    }
                });
                return false;
            });
            $('.add-transaction-but').click(function() {
                $('.add-transaction').show();
                return false;
            });
            $('.transfer-but').click(function() {
                $('.transfer-container').show();
                return false;
            });

        },
        deletePromosInit: function()
        {
            $('.delete-promos-but').click(function() {
                var $form = $('.promos-form');
                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serializeArray(),
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR) {
                        $.referral.message(data, {content: $('.del-promos-result')});
                        if (data.status == 'ok') {
                            $.referral.defaultAction();
                        }
                    },
                    error: function(jqXHR, errorText) {
                    }
                });
            });
        },
        addPaymentAction: function() {
            this.load('?plugin=referral&action=addPayment', function() {
                $.referral.initAddPaymentHandler();
            });
        },
        addReferralAction: function() {
            this.load('?plugin=referral&action=addReferral', function() {
                $.referral.initAddReferralHandler();
            });
        },
        initAddPaymentHandler: function() {
            var autocompete_input = $("#customer-autocomplete");
            autocompete_input.autocomplete({
                source: function(request, response) {
                    var term = request.term;
                    $.getJSON('?action=autocomplete&type=contact', request, function(r) {
                        response(r);
                    });
                },
                delay: 300,
                minLength: 3,
                select: function(event, ui) {
                    var item = ui.item;
                    if (item.value) {
                        $('#s-customer-id').val(item.value);
                        $('.field-group').html('<i class="icon16 loading"></i>');
                        $.ajax({
                            type: 'POST',
                            url: '?plugin=referral&action=contactForm',
                            dataType: 'json',
                            data: {
                                id: item.value
                            },
                            success: function(data, textStatus, jqXHR) {
                                if (data.status == 'ok') {
                                    $('.field-group').html(data.data.html_form);
                                    $('.balance_val').html(data.data.balance);
                                    $('.date_input').val(data.data.date);
                                } else {
                                    alert(data.errors);
                                }
                            }
                        });
                    }
                    return false;
                },
                focus: function(event, ui) {
                    this.value = ui.item.name;
                    return false;
                }
            });
            $('form.add-payment-form').submit(function() {
                $('#response-status').html('<i style="vertical-align:middle" class="icon16 loading"></i>');
                $('#response-status').show();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $('#response-status').html('<i style="vertical-align:middle" class="icon16 yes"></i>Сохранено');
                            $('#response-status').css('color', '#008727');
                        } else {
                            $('#response-status').html('<i style="vertical-align:middle" class="icon16 no"></i>' + data.errors);
                            $('#response-status').css('color', '#FF0000');
                        }
                        setTimeout(function() {
                            $('#response-status').hide();
                        }, 3000);
                    }
                });
                return false;
            });
        },
        initAddReferralHandler: function() {
            var autocompete_input = $("#customer-autocomplete");
            autocompete_input.autocomplete({
                source: function(request, response) {
                    var term = request.term;
                    $.getJSON('?action=autocomplete&type=contact', request, function(r) {
                        response(r);
                    });
                },
                delay: 300,
                minLength: 3,
                select: function(event, ui) {
                    var item = ui.item;
                    if (item.value) {
                        $('#s-customer-id').val(item.value);
                        $('.field-group').html('<i class="icon16 loading"></i>');
                        $.ajax({
                            type: 'POST',
                            url: '?plugin=referral&action=contactForm',
                            dataType: 'json',
                            data: {
                                id: item.value
                            },
                            success: function(data, textStatus, jqXHR) {
                                if (data.status == 'ok') {
                                    $('.field-group').html(data.data.html_form);
                                    $('.balance_val').html(data.data.balance);
                                    $('.date_input').val(data.data.date);
                                } else {
                                    alert(data.errors);
                                }
                            }
                        });
                    }
                    return false;
                },
                focus: function(event, ui) {
                    this.value = ui.item.name;
                    return false;
                }
            });
            $('form.add-referral-form').submit(function() {
                $('#response-status').html('<i style="vertical-align:middle" class="icon16 loading"></i>');
                $('#response-status').show();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $('#response-status').html('<i style="vertical-align:middle" class="icon16 yes"></i>Сохранено');
                            $('#response-status').css('color', '#008727');
                        } else {
                            $('#response-status').html('<i style="vertical-align:middle" class="icon16 no"></i>' + data.errors);
                            $('#response-status').css('color', '#FF0000');
                        }
                        setTimeout(function() {
                            $('#response-status').hide();
                        }, 3000);
                    }
                });
                return false;
            });
        },
        addPromoAction: function(id) {
            if (!id) {
                id = '';
            }
            this.load('?plugin=referral&action=addPromo&id=' + id, function() {
                $('#form-add-promo').submit(function() {
                    $.referral.savePromoHandler(this);
                    return false;
                });
                $.referral.fileUploadInit();

            });
        },
        addTransactionAction: function(id) {
            if (!id) {
                id = '';
            }
            this.load('?plugin=referral&action=addTransaction&id=' + id, function() {
            });
        },
        fileUploadInit: function()
        {

            $('.fileupload').fileupload({
                url: '?plugin=referral&action=savePromo',
                dataType: 'json',
                done: function(e, data) {

                    $(this).parent().find('.slide-result').html('');
                    $('.loading').remove();
                    if (data.result.data) {
                        $(this).closest('.value').find(".preview").html('<img src="' + data.result.data.preview + '" />');
                        if (data.result.data.id) {
                            $('#promo_id').val(data.result.data.id);
                        }
                    } else {
                        $.referral.message(data.result, {content: $(this).parent().find('.promo-result')});
                    }
                },
                fail: function(e, data) {
                    $('.loading').remove();
                    $.referral.message(data.result, {content: $(this).parent().find('.promo-result')});
                },
                start: function(e, data) {
                    $(this).parent().append('<span class="loading"><i class="icon16 loading"></i>Loading...</span>');
                },
            });
        },
        savePromoHandler: function(form)
        {
            var $form = $(form);
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serializeArray(),
                iframe: true,
                dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    $.referral.message(data);
                    if (data.data && data.data.id) {
                        $('#promo_id').val(data.data.id);
                    }
                },
                error: function(jqXHR, errorText) {
                }
            });
        },
        message: function(data, options)
        {
            options = options || {};

            if (data.status == 'ok') {
                var mes = 'Сохранено';
                if (data.data.message) {
                    mes = data.data.message;
                    (options.content || $('#form-result')).css('color', 'green');
                }
                (options.content || $('#form-result')).html('<i class="icon16 yes" style="vertical-align:middle"></i>' + mes);
            } else if (data.status == 'fail') {
                var mes = 'Ошибка';
                if (data.errors) {
                    mes = data.errors[0][0];
                }
                (options.content || $('#form-result')).html('<i class="icon16 no" style="vertical-align:middle"></i>' + mes);
                (options.content || $('#form-result')).css('color', 'red');
            }
            (options.content || $('#form-result')).show();
            setTimeout('$("#form-result").hide()', 5000);
        },
        /** Current hash */
        getHash: function() {
            return this.cleanHash();
        },
        /** Make sure hash has a # in the begining and exactly one / at the end.
         * For empty hashes (including #, #/, #// etc.) return an empty string.
         * Otherwise, return the cleaned hash.
         * When hash is not specified, current hash is used. */
        cleanHash: function(hash) {
            if (typeof hash == 'undefined') {
                hash = window.location.hash.toString();
            }

            if (!hash.length) {
                hash = '' + hash;
            }
            while (hash.length > 0 && hash[hash.length - 1] === '/') {
                hash = hash.substr(0, hash.length - 1);
            }
            hash += '/';

            if (hash[0] != '#') {
                if (hash[0] != '/') {
                    hash = '/' + hash;
                }
                hash = '#' + hash;
            } else if (hash[1] && hash[1] != '/') {
                hash = '#/' + hash.substr(1);
            }

            if (hash == '#/') {
                return '';
            }

            return hash;
        },
        load: function(url, options, fn) {
            if (typeof options === 'function') {
                fn = options;
                options = {};
            } else {
                options = options || {};
            }
            var r = Math.random();
            this.random = r;
            var self = this;



            (options.content || $("#slider-content")).html('<div class="block triple-padded"><i class="icon16 loading"></i>Loading...</div>');
            return  $.get(url, function(result) {
                if ((typeof options.check === 'undefined' || options.check) && self.random != r) {
                    // too late: user clicked something else.
                    return;
                }

                (options.content || $("#slider-content")).removeClass('bordered-left').html(result);
                if (typeof fn === 'function') {
                    fn.call(this);
                }

            });
        },
        onPageNotFound: function() {
            //this.defaultAction();
        }
    };



})(jQuery);