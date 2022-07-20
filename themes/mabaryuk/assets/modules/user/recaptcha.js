$(document).on('ready', function() {
    grecaptcha.ready(function() {
        $('#formRegisterHero').on('submit', function(e) {
            e.preventDefault()
            grecaptcha.execute(site_key, {action: 'register_user_action'}).then(function(token) {
                $('#formRegisterHeroToken').val(token)
                $('#formRegisterHeroAction').val('register_user_action')
                $("#btnSubmitRegisterUser").prop('disabled', true)
                $("#formRegisterHero").request('onRegister', {
                    form: '#formRegisterHero',
                    flash: true,
                    loading: $.oc.stripeLoadIndicator,
                    handleErrorMessage(message) {
                        console.log(message)
                        $.oc.flashMsg({ text: message, class: 'error' })
                    },
                    success() {
                        $('#register_modal').modal('hide');
                        $('#email_sent_modal').modal('show');
                    },
                    complete() {
                        $("#btnSubmitRegisterUser").prop('disabled', false)
                    }
                })
            });
        })

        $('#formRegisterCompany').on('submit', function(e) {
            e.preventDefault()
            grecaptcha.execute(site_key, {action: 'register_company_action'}).then(function(token) {
                $('#formRegisterCompanyToken').val(token)
                $('#formRegisterCompanyAction').val('register_company_action')
                $("#btnSubmitRegisterCompany").prop('disabled', true)
                $("#formRegisterCompany").request('onCreateCompany', {
                    form: '#formRegisterCompany',
                    flash: true,
                    loading: $.oc.stripeLoadIndicator,
                    handleErrorMessage(message) {
                        console.log(message)
                        $.oc.flashMsg({ text: message, class: 'error' })
                    },
                    success() {
                        $('#register_modal').modal('hide');
                        $('#email_sent_modal').modal('show');
                    },
                    complete() {
                        $("#btnSubmitRegisterCompany").prop('disabled', false)
                    }
                })
            });
        })

        $('#formReferFriend').on('submit', function(e) {
            e.preventDefault()
            grecaptcha.execute(site_key, {action: 'refer_friend_action'}).then(function(token) {
                $('#formReferFriendToken').val(token)
                $('#formReferFriendAction').val('refer_friend_action')
                $("#btnSubmitReferFriend").prop('disabled', true)
                $("#formReferFriend").request('onRefer', {
                    form: '#formReferFriend',
                    flash: true,
                    loading: $.oc.stripeLoadIndicator,
                    update: { 
                        'jobs/input-talent-answer': '.input_talent_answer' 
                    },
                    handleErrorMessage(message) {
                        console.log(message)
                        $.oc.flashMsg({ text: message, class: 'error' })
                    },
                    handleFlashMessage: function(message, type) {
                        $.oc.flashMsg({ text: message, class: type })
                    },
                    complete() {
                        $("#btnSubmitReferFriend").prop('disabled', false);
                    }
                })
            });
        })

        $('#formReferAnswer').on('submit', function(e) {
            e.preventDefault()
            grecaptcha.execute(site_key, {action: 'refer_answer_action'}).then(function(token) {
                $('#formReferAnswerToken').val(token)
                $('#formReferAnswerAction').val('refer_answer_action')
                $("#btnSubmitReferAnswer").prop('disabled', true)
                $("#formReferAnswer").request('onAnswerRefer', {
                    form: '#formReferAnswer',
                    flash: true,
                    loading: $.oc.stripeLoadIndicator,
                    update: { 'jobs/referral': '^#list-referral' },
                    handleErrorMessage(message) {
                        console.log(message)
                        $.oc.flashMsg({ text: message, class: 'error' })
                    },
                    handleFlashMessage: function(message, type) {
                        $.oc.flashMsg({ text: message, class: type })
                    },
                    complete() {
                        $("#btnSubmitReferAnswer").prop('disabled', false);
                    }
                })
            });
        })
    });
})
