<div class="table-responsive">
    <table class="table" id="usersManagers-table">
        <thead>
            <tr>
                
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($usersManagers as $usersManager)
            <tr>
                
                <td>
                    {!! Form::open(['route' => ['usersManagers.destroy', $usersManager->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('usersManagers.show', [$usersManager->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('usersManagers.edit', [$usersManager->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
