{!! Form::open(['route' => 'authors.store']) !!}
    @include ('authors.form', ['submitButtonText' => 'Add Author'])
{!! Form::close() !!}
