{!! Form::open(['route' => 'articles.store']) !!}
    @include ('articles.form', ['submitButtonText' => 'Add Article'])
{!! Form::close() !!}
