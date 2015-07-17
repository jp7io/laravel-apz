<h1>Authors</h1>
{!! link_to_route('authors.create', 'New Author') !!}
<table border="1">
    <tr>
        <th>Edit</th>
        <th>Delete</th>
        <th>Name</th>
    </tr>
    @foreach ($authors as $author)
        <tr>
            <td>{!! link_to_route('authors.edit', 'Edit', $author->id) !!}</td>
            <td>
                {!! Form::open(['method' => 'DELETE', 'route' => ['authors.destroy', $author->id]]) !!}
                    <button type="submit">Delete</button>
                {!! Form::close() !!}
            </td>
            <td>{!! link_to_route('authors.show', $author->name, $author->id) !!}</td>
        </tr>
    @endforeach
</table>
