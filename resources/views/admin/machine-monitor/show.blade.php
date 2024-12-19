<table>
    <thead>
        <tr>
            <th>ID Mesin</th>
            <th>Nama Mesin</th>
            <th>Status</th>
            <th>Tanggal Pembaruan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($machines as $machine)
        <tr>
            <td>{{ $machine->id }}</td>
            <td>{{ $machine->name }}</td>
            <td>{{ $machine->status }}</td>
            <td>{{ $machine->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
