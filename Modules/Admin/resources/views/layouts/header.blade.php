<style>
    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    /* Hide loader when page is ready */
    #loader.hidden {
        opacity: 0;
        visibility: hidden;
    }
</style>
<div id="loader">
    <img src="{{ url('assets/images/Frame.png') }}" alt="AdminLTE Logo" style="width:75 px;" class="brand-image ">
</div>


<!--
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item"> <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a> </li>
        <li class="nav-item d-none d-sm-inline-block"> <a class="nav-link" href="javascript:;" role="button">Welcome
            <b>{{ Auth::guard('admin')->user()->name ?? '' }}</b>
        </a> </li>
    </ul>
</nav>
-->

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" href="javascript:;" role="button">
                Welcome <b>{{ Auth::guard('admin')->user()->name ?? '' }}</b>
            </a>
        </li>
    </ul>

    <!-- RIGHT SIDE -->

</nav>
