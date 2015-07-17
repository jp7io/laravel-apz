<div>
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title') !!}
</div>
<div>
    {!! Form::label('content', 'Content:') !!}
    {!! Form::textArea('content') !!}
</div>
{!! Form::submit($submitButtonText) !!}
