<div>
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name') !!}
</div>
<div>
    {!! Form::label('email', 'E-mail:') !!}
    {!! Form::text('email') !!}
</div>
{!! Form::submit($submitButtonText) !!}
