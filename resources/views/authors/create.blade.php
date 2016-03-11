@extends($layout)

@section('content')
  {!! Form::open(['route' => 'authors.store']) !!}
      @include ('authors.form', ['submitButtonText' => 'Add Author'])
  {!! Form::close() !!}
@endsection
