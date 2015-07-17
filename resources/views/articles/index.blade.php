@extends($template)

@section('content')
    <h1>Articles</h1>
    {!! link_to_route('articles.create', 'New Article', null, ['class' => 'btn btn-primary btn-lg']) !!}
    <table class="table">
        <tr>
            <th>Edit</th>
            <th>Delete</th>
            <th>Title</th>
        </tr>
        @foreach ($articles as $article)
            <tr>
                <td>{!! link_to_route('articles.edit', 'Edit', $article->id, ['class' => 'btn btn-default']) !!}</td>
                <td>
                    {!! Form::open(['method' => 'DELETE', 'route' => ['articles.destroy', $article->id], ]) !!}
                        <button type="submit" class="btn btn-warning">Delete</button>
                    {!! Form::close() !!}
                </td>
                <td>{!! link_to_route('articles.show', $article->title, $article->id) !!}</td>
            </tr>
        @endforeach
    </table>
@endsection
