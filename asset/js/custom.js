/**
 * Javascript event for menu and search
 * @author UTC.KongLTN
 * @author UTC.HuyTD
 * Last Update on Dec 23, 2015
 */

jQuery(document).ready(function () {
    // Fix sort title of session detail
    var currentUrl = $(location).attr('pathname');
    var checkInDetail = currentUrl.indexOf('observation/detail', currentUrl);
    var checkInPlanedDetail = currentUrl.indexOf('observation/plannedDetail', currentUrl);
    var checkInPastDetail = currentUrl.indexOf('observation/pastDetail', currentUrl);

    if (checkInDetail >= 0 || checkInPlanedDetail >= 0 || checkInPastDetail >= 0) {
        $('#titleInHeaderBar').addClass('title-detail');
    }
    // # Fix sort title of session detail


    jQuery('.collapsed').click(function () {

        $("#navbar-collapse-main").toggleClass("open");
        $('.search-toggle').removeClass('open-search');
        $('.dropdown-menu').removeClass('open');
        $('.background-hiddenmenu').toggleClass('open');
        $('.background-hiddensearch').removeClass('open');
        $('.background-hiddenuser').removeClass('open');
    });

    jQuery('.dropdown-toggle').click(function () {
        $('.dropdown-menu').toggleClass('open');
        $('.search-toggle').removeClass('open-search');
        $('#navbar-collapse-main').removeClass('open');
        $('.background-hiddenuser').toggleClass('open');
        $('.background-hiddenmenu').removeClass('open');
        $('.background-hiddensearch').removeClass('open');
    });
    jQuery('.notification').click(function () {
        $('.dropdown-menu').removeClass('open');
        $('.search-toggle').removeClass('open-search');
        $('#navbar-collapse-main').removeClass('open');
        $('.background-hiddenuser').toggleClass('open');
        $('.background-hiddenmenu').removeClass('open');
        $('.background-hiddensearch').removeClass('open');
    });

    jQuery('.dropdown-search1').click(function () {
        if ($('#isGuest').val() === "1") {
            window.location.replace('/auth/user/login');
        } else {
            $('.search-toggle').toggleClass('open-search');
            $('.dropdown-menu').removeClass('open');
            $('#navbar-collapse-main').removeClass('open');
            $('.background-hiddensearch').toggleClass('open');
            $('.background-hiddenmenu').removeClass('open');
            $('.background-hiddenuser').removeClass('open');
        }
    });
	
	// Bind click event for plus button in archive screen
	jQuery('.btn-total-archive').click(function () {
        var checkDevice = isMobile.webView();
		//var checkDevice = isMobile.webViewAndroid();

        if (checkDevice) {
            $('#uploadArchiveInput').addClass('display-none');
            $('#chosenArchiveFileApp').removeClass('display-none');
            $('#buttonOkNewFileArchive').addClass('display-none');
        } else {
            $('#uploadArchiveInput').removeClass('display-none');
            $('#chosenArchiveFileApp').addClass('display-none');
            $('#buttonOkNewFileArchive').removeClass('display-none');
        }
    });

    jQuery('.btn-total').click(function () {
        $('.btn-close').removeClass('display-none');
        //var checkDevice = isMobile.iOSAndAndroid();
        var checkDevice = isMobile.webView();

        if (checkDevice) {
            $('.btn-video-app').removeClass('display-none');
            $('.btn-camera-app').removeClass('display-none');
            $('#uploadArchiveInput').addClass('display-none');
            $('#chosenArchiveFileApp').removeClass('display-none');
            $('#buttonOkNewFileArchive').addClass('display-none');
        } else {
            $('.btn-video').removeClass('display-none');
            $('.btn-camera').removeClass('display-none');
            $('#uploadArchiveInput').removeClass('display-none');
            $('#chosenArchiveFileApp').addClass('display-none');
            $('#buttonOkNewFileArchive').removeClass('display-none');
        }
        $('.btn-comment').removeClass('display-none');
    });

    jQuery('.btn-close').click(function () {
        $('.btn-close').addClass('display-none');
        //var checkDevice = isMobile.iOSAndAndroid();
        var checkDevice = isMobile.webView();
        if (checkDevice) {
            $('.btn-video-app').addClass('display-none');
            $('.btn-camera-app').addClass('display-none');
        } else {
            $('.btn-video').addClass('display-none');
            $('.btn-camera').addClass('display-none');
        }
        $('.btn-comment').addClass('display-none');
    });
    jQuery('.background-hiddensearch').click(function () {
        $('.background-hiddensearch').toggleClass('open');
        $('.search-toggle').removeClass('open-search');
        $('.dropdown-menu').removeClass('open');
        $('#navbar-collapse-main').removeClass('open');
        $('.background-hiddenmenu').removeClass('open');
        $('.background-hiddenuser').removeClass('open');
    });
    jQuery('#cancelSearchBar').click(function () {
        $('.background-hiddensearch').toggleClass('open');
        $('.search-toggle').removeClass('open-search');
        $('.dropdown-menu').removeClass('open');
        $('#navbar-collapse-main').removeClass('open');
        $('.background-hiddenmenu').removeClass('open');
        $('.background-hiddenuser').removeClass('open');
    });
    jQuery('.background-hiddenmenu').click(function () {
        $('.background-hiddenmenu').toggleClass('open');
        $('.search-toggle').removeClass('open-search');
        $('.dropdown-menu').removeClass('open');
        $('#navbar-collapse-main').removeClass('open');
        $('.background-hiddensearch').removeClass('open');
        $('.background-hiddenuser').removeClass('open');
    });
    jQuery('.background-hiddenuser').click(function () {
        $('.background-hiddenuser').toggleClass('open');
        $('.search-toggle').removeClass('open-search');
        $('.dropdown-menu').removeClass('open');
        $('#navbar-collapse-main').removeClass('open');
        $('.background-hiddenmenu').removeClass('open');
        $('.background-hiddensearch').removeClass('open');
    });

    // Hide notification menu when click outside
    $("#showNumberNotify").click(function () {
        $('#allNotify_UL').toggle();
    });
    $(document).on("click", function (event) {
        var $trigger = $("#showNumberNotify");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $("#allNotify_UL").hide();
        }
    });
    $('#allNotify_UL').click(function (event) {
        event.stopPropagation();
    });
    // # Hide notification menu when click outside

    $("input.toogleswitch").each(function (index, value) {
        $(this).toggleSwitch();
    });

    window.asd = $('.SlectBox').SumoSelect({csvDispCount: 2});

    $('#datepicker').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        formatDate: 'd-m-Y'
    });

    $('#date_timepicker_start').datetimepicker({
        format: 'd-m-Y',
        onShow: function (ct) {
            this.setOptions({
                maxDate: $('#date_timepicker_end').val() ? $('#date_timepicker_end').val() : false
            });
        },
        timepicker: false
    });

    $('#date_timepicker_end').datetimepicker({
        format: 'd-m-Y',
        onShow: function (ct) {
            this.setOptions({
                minDate: $('#date_timepicker_start').val() ? $('#date_timepicker_start').val() : false
            });
        },
        timepicker: false
    });

    $(".drop-ic").hover(
        function () {
            if ($(this).is('.show-op')) {
                $(this).removeClass('show-op');
            } else {
                $(this).addClass('show-op');
            }
        }, function () {
            if ($(this).is('.show-op')) {
                $(this).removeClass('show-op');
            } else {
                $(this).addClass('show-op');
            }
        }
    );

    $('.btn-nav').click(function () {
        $('body').toggleClass("tg-mobi");
    });


});
