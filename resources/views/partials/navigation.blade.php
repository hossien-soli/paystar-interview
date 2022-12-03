
<nav class="navbar navbar-expand-lg bg-white">
    <div class="container">
        <a class="navbar-brand" href="{{ route('main.home') }}">
            <img src="{{ Vite::asset('resources/assets/logo.svg') }}" width="95" alt="logo">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item me-2">
                    <a class="nav-link" href="{{ route('main.home') }}">
                        <i class="mdi mdi-home-outline"></i> خانه
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-help-circle-outline"></i> درباره ما
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-phone-outline"></i> تماس با ما
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>