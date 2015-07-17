<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Laravel Apz</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>{!! link_to_route('articles.index', 'Articles') !!}</li>
                <li>{!! link_to_route('authors.index', 'Authors') !!}</li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="navbar-text">Sao Paulo: {{ $weather->temp }} Celsius. Last update {{ $weather->updated_at }}</li>
            </ul>
        </div>
    </div>
</nav>
