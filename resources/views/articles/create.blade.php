@extends($layout)

@section('content')
    {!! Form::open(['route' => 'articles.store', 'data-remote' => $remote, 'id' => 'articles-form']) !!}
        @include('articles.form', ['submitButtonText' => 'Add Article'])
    {!! Form::close() !!}
@endsection
