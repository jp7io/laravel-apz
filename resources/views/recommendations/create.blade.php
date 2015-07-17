@extends($template)

@section('content')
    @include('shared.alert')
    <h4>Recommend "{{ $article->title }}" by e-mail</h4>
    <p>
        {!! Form::open(['route' => ['articles.recommendations.store', $article->id]]) !!}
            <div class='form-group'>
                {!! Form::label('email', 'Email:') !!}
                {!! Form::text('email', null, ['class' => 'form-control']) !!}
            </div>
            {!! Form::submit('Send Recommendation', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </p>
@endsection
