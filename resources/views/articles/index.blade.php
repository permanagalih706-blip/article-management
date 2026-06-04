<!DOCTYPE html>
<html>
<head>
    <title>Daftar Artikel</title>
</head>
<body>

<h1>Daftar Artikel</h1>

<a href="/articles/create">
    Tambah Artikel
</a>

<form method="GET" action="/articles">

    <input
        type="text"
        name="search"
        placeholder="Cari judul artikel">

    <select name="author">
        <option value="">Semua Author</option>

        @foreach($users as $user)
            <option value="{{ $user->id }}">
                {{ $user->name }}
            </option>
        @endforeach

    </select>

    <button type="submit">
        Cari
    </button>

</form>

<br>

<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Judul</th>
        <th>Author</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    @foreach($articles as $article)
    <tr>
    <td>{{ $article->id }}</td>
    <td>{{ $article->title }}</td>
    <td>{{ $article->user->name }}</td>
    <td>{{ $article->status }}</td>
    <td>

    <a href="/articles/{{ $article->id }}/edit">
        Edit
    </a>

    <form action="/articles/{{ $article->id }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')

        <button type="submit">
            Hapus
        </button>
    </form>

</td>
</tr>
    @endforeach

</table>

</body>
</html>