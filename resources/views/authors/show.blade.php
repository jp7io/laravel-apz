@extends($layout)

@section('content')
  <div>
      <h1>{{ $author->name }}</h1>
      <p>{{ $author->email }}</p>
      {!! link_to_route('authors.index', 'Authors') !!}
  </div>
@endsection
