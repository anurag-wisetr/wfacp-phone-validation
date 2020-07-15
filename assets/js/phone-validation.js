jQuery(document).ready(function ($) {

        class phoneValidater {

            constructor() {
                this.regexes = phone_data;
                this.country = 'us';
                this.initPhoneValidator();
            }

            initPhoneValidator() {
                console.log(this.regexes);
                var classObj = this;
                if ($('#billing_country').length > 0) {
                    let countryCode = $('#billing_country').val();
                    classObj.country = countryCode.toLowerCase();
                    if (typeof classObj.regexes[classObj.country] === 'undefined') {
                        classObj.country = '';
                    }

                    $(document.body).on('change', '#billing_country', function () {
                        countryCode = $(this).val();
                        countryCode = countryCode.toLowerCase();
                        if (typeof classObj.regexes[countryCode] === 'undefined') {
                            classObj.country = '';
                            return;
                        }

                        $('#flag').removeAttr("class");
                        classObj.country = countryCode.toLowerCase();

                        $.each(classObj.regexes, function (country, details) {
                            if (classObj.country != country) {
                                return;
                            }
                            classObj.setFlag('+' + details.code);
                            var phone = $('#billing_phone').val();
                            if (0 == phone.indexOf("+")) {
                                $('#billing_phone').val('+' + details.code);
                            }
                            return false;
                        });
                    });
                }
                $("#billing_phone_field .woocommerce-input-wrapper").prepend("<div id='flag' class='iti__flag iti__" + classObj.country + "' data-country-code='" + classObj.country + "'></div><div class='iti__arrow flag-dropdown-icon'></div>");
                if ($('#billing_phone').length > 0) {
                    var dialcode = $('#billing_phone').val();
                    classObj.setFlag(dialcode);
                }
                $(document.body).on('keyup', '#billing_phone', function () {
                    var dialcode = $(this).val();
                    classObj.setFlag(dialcode);
                });

                $(document.body).on('change', '#billing_phone', function () {
                    var phone = $(this).val();
                    phone = phone.replace('+' + classObj.dialcode, "");
                    var valid = classObj.validatePhoneNumber(phone);
                    if (!valid) {
                        $(this).css({"border-color": "#d50000"});
                    } else {
                        $(this).css({"border-color": "#bfbfbf"});
                    }
                });

                wfacp_frontend.hooks.addFilter('wfacp_field_validated', (validated, $this, $parent) => {
                    return this.validateField(validated, $this, $parent);
                });
                wfacp_frontend.hooks.addFilter('wfacp_field_error_message', (my_msg, el, input) => {
                    return this.sendErrorMessage(my_msg, el, input);
                });

                $('.flag-dropdown-icon, .woocommerce-input-wrapper .iti__flag').on('click', function () {
                    $('.wfacp-flag-list').toggle();
                });

                $(document.body).mouseup(function (e) {
                    var container = $(".wfacp-flag-list");
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        container.hide();
                    }
                });


                $(document.body).on('click', '.iti__country', function () {
                    var dial_code = $(this).data('dial-code');
                    var country = $(this).data('country');
                    var country_name = $(this).data('country-name');
                    $('#flag').removeAttr("data-country-code");
                    $('#flag').attr("class", "iti__flag iti__" + country);
                    $('#flag').attr("title", country_name);
                    $('#flag').attr("data-country-code", country);
                    $('#billing_phone').val('+' + dial_code);
                    $('.wfacp-flag-list').hide();
                });
            }

            setFlag(dialcode) {
                var classobj = this;
                if (-1 >= dialcode.indexOf("+")) {
                    return;
                }
                dialcode = dialcode.slice(1);

                // $('.iti__country[data-dial-code="'+dialcode+'"]').length
                var countryDetails = false;
                $.each(classobj.regexes, function (country, details) {
                    details.country = country;
                    countryDetails = classobj.checkCodeExist(details, dialcode);
                    if (countryDetails) {
                        return false;
                    }
                });

                $('#flag').removeAttr("data-country-code");
                if (countryDetails === false) {
                    return;
                }
                $('#flag').removeAttr("class");
                this.country = countryDetails.country;
                $('#flag').attr("class", "iti__flag iti__" + countryDetails.country);
                $('#flag').attr("title", countryDetails.name);
                $('#flag').attr("data-country-code", countryDetails.country);
            }

            validatePhoneNumber(phoneNumber) {
                var country = $('#flag').attr('data-country-code');
                if (typeof this.regexes[country] === 'undefined') {
                    return true;
                }
                var pattern = new RegExp(this.regexes[country]['pattern'].slice(1, -1));
                if (phoneNumber.match(pattern)) {
                    return true;
                } else {
                    return false;
                }
            }

            checkCodeExist(details, dialcode) {
                if (dialcode.indexOf(details.code) == 0) {
                    if (typeof details.areaCodes !== "undefined") {
                        var areaaCode = dialcode.replace(details.code, '');
                        var codeExist = '';
                        for (let i = 0; i <= details.areaCodes.length; i++) {
                            if (areaaCode.indexOf(details.areaCodes[i]) == 0) {
                                codeExist = true;
                                break;
                            }
                        }
                        if (codeExist) {
                            return details;
                        }
                    } else {
                        return details;
                    }
                } else {
                    $('#flag').removeClass('iti__' + details.country);
                    return false;
                }
            }

            validateField(validated, $this, $parent) {
                var tel = $('#billing_phone').val();
                if ($this.attr('id') == 'billing_phone') {
                    if (null !== tel) {
                        let phone_number = $this.val();
                        if ('' !== phone_number && !this.validatePhoneNumber(phone_number)) {
                            $parent.removeClass('woocommerce-validated').addClass('woocommerce-invalid woocommerce-invalid-required-field');
                            validated = false;
                        }
                    }
                }
                return validated;
            }


            sendErrorMessage(my_msg, $this, input) {
                if (input.attr('id') == 'billing_phone') {
                    if (null !== this.tel) {
                        let label_el = $this.children('label');
                        if (label_el.length === 0) {
                            label_el = $this.find('label').eq(0);
                        }
                        let label = label_el.clone().children().remove().end().text();
                        my_msg = '<b>' + label + '</b> is not valid.';
                    }
                }
                return my_msg;
            }
        }

        new

        phoneValidater();
    }
);