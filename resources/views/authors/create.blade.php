@extends($template)

@section('content')
  {!! Form::open(['route' => 'authors.store', 'id' => 'authors-form']) !!}
      @include ('authors.form', ['submitButtonText' => 'Add Author'])
  {!! Form::close() !!}
@endsection
