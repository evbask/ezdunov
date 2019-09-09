<!DOCTYPE html>
<html lang="en">
<?php

if (!isset($nolayers)) {
    $nolayers = false;
}
if (!isset($nonavigation)) {
    $nonavigation = false;
}
?>
@include('common.head', ['nolayers' => $nolayers, 'nonavigation' => $nonavigation])

<body class="double-diagonal dark @yield('body_class') home">
<script src="/js/jquery-2.2.4.min.js"></script>
<!-- Grandin -->
<script src="/js/grandin/jquery.slimscroll.js"></script>
<script src="/js/grandin/init.js"></script>

    <!-- Preloader Starts -->
    <div class="preloader" id="preloader">
        <div class="logopreloader">
            <img src="https://via.placeholder.com/159x28" alt="logo">
        </div>
        <div class="loader" id="loader"></div>
    </div>
    <!-- Preloader Ends -->
    <!-- Page Wrapper Starts -->
    <div class="wrapper theme-6-active pimary-color-pink">

        @include('common.header')
        <div class="fixed-sidebar-left">
            @include('parts.home_sidebar')
        </div>

        <div class="page-wrapper">
            <script src="{{asset('js/grandin/jquery.toast.min.js')}}"></script>
            <script src="{{asset('js/jquery.inputmask.bundle.js')}}"></script>
                @yield('content')
        </div>
        @include('forms.message')
    </div>
    <!-- Wrapper Ends -->

</body>
     <!-- Template JS Files -->
     {{--<script src="/js/plugins/jquery.easing.1.3.js"></script>--}}
     <script src="/js/plugins/bootstrap.min.js"></script>
     {{--<script src="/js/plugins/jquery.bxslider.min.js"></script>--}}
     {{--<script src="/js/plugins/jquery.filterizr.js"></script>--}}
     {{--<script src="/js/plugins/jquery.magnific-popup.min.js"></script>--}}
     <script src="/js/plugins/validator.min.js"></script>




    <!-- Библиотека, работающая с буфером обмена -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.4.0/clipboard.min.js"></script>


    {{--<script src="js/grandin/jquery.waypoints.min.js"></script>--}}
    {{--<script src="js/grandin/jquery.counterup.min.js"></script>--}}
     <!-- Revolution Slider Main JS Files -->
     {{--<script src="/js/plugins/revolution/js/jquery.themepunch.tools.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/jquery.themepunch.revolution.min.js"></script>--}}


     <!-- Revolution Slider Extensions -->

     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.actions.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.carousel.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.kenburn.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.migration.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.navigation.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.parallax.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.slideanims.min.js"></script>--}}
     {{--<script src="/js/plugins/revolution/js/extensions/revolution.extension.video.min.js"></script>--}}

     <!-- Main JS Initialization File -->
     <script src="/js/custom.js"></script>
</html>
