{!! Form::model($article, ['method' => 'PATCH', 'route' => ['articles.update', $article->id]]) !!}
    @include ('articles.form', ['submitButtonText' => 'Edit Article'])
{!! Form::close() !!}
