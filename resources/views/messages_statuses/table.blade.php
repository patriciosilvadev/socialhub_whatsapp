<div class="table-responsive">
    <table class="table" id="messagesStatuses-table">
        <thead>
            <tr>
                <th>Name</th>
        <th>Description</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($messagesStatuses as $messagesStatus)
            <tr>
                <td>{!! $messagesStatus->name !!}</td>
            <td>{!! $messagesStatus->description !!}</td>
                <td>
                    {!! Form::open(['route' => ['messagesStatuses.destroy', $messagesStatus->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('messagesStatuses.show', [$messagesStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('messagesStatuses.edit', [$messagesStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
