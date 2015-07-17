<div>
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title') !!}
</div>
<div>
    {!! Form::label('content', 'Content:') !!}
    {!! Form::textArea('content') !!}
</div>
<div>
    {!! Form::label('author_id', 'Author:') !!}
    {!! Form::select('author_id', $authors, $article->author_id) !!}
</div>
{!! Form::submit($submitButtonText) !!}
