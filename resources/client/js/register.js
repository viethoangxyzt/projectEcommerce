$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            token: "24d5b95c-7cde-11ed-be76-3233f989b8f3"
        },
    });

    $(document).on('change', '#city', function(){
        $('#district').html("");
        $('#ward').html("");
        //get list province
        getProvind();
    });

    $(document).on('change', '#district', function(){
        $('#ward').html("");
        // get list ward
        getWard();
    });
    //check click btn submit
    $(document).on('submit', '#form__js', function(){
        //display loading
        $('#loading__js').css('display', 'flex');
    });

    const rules = $("#form-data").data("rules");
    const messages = $("#form-data").data("messages");

    $.validator.addMethod("checklower", function (value) {
        return value ? /[a-z]/.test(value) : true;
    });
    $.validator.addMethod("checkupper", function (value) {
        return value ? /[A-Z]/.test(value) : true;
    });
    $.validator.addMethod("checkdigit", function (value) {
        return value ? /[0-9]/.test(value) : true;
    });
    $.validator.addMethod("checkspecialcharacter", function (value) {
        return value ? /[%#@_\-]/.test(value) : true;
    });

    $("#form__js").validate({
        rules: rules ?? "",
        messages: messages ?? "",
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        submitHandler: (form) => {
            form.submit();
            $('#loading__js').css('display', 'flex');
        },
    });
});

// fucntion get district
function getProvind()
{
    let provinceId = $('#city').val();
    $.ajax({
        type: 'GET',
        url: 'https://online-gateway.ghn.vn/shiip/public-api/master-data/district',
        data: {
            province_id: provinceId
        }
    }).done((respones) => {
        let option = '';
        //add data to district select
        respones.data.forEach(element => {
            option = `<option value="${element.DistrictID}">${element.DistrictName}</option>`
            $('#district').append(option);
        });
        getWard();
    });
}

//function get ward
function getWard()
{
    let district_id  = $('#district').val();
    $.ajax({
        type: 'GET',
        url: 'https://online-gateway.ghn.vn/shiip/public-api/master-data/ward',
        data: {
            district_id : district_id 
        }
    }).done((respones) => {
        let option = '';
        //add data to ward select
        respones.data.forEach(element => {
            option = `<option value="${element.WardCode}">${element.NameExtension[0]}</option>`
            $('#ward').append(option);
        });
    });
}