<?php
$data = [];

// Read JSON file
if (file_exists('books.json')) {
    $data = json_decode(file_get_contents('books.json'), true);
}

// Save data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['title']) && !empty($_POST['author'])) {
        $newBook = [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
        ];

        $data[] = $newBook;

        file_put_contents('books.json', json_encode($data, JSON_PRETTY_PRINT));

        header('Location: index.php');
        exit;
    }
}

// Delete record
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    if (isset($data[$index])) {
        unset($data[$index]);
        $data = array_values($data); // Re-index the array
        file_put_contents('books.json', json_encode($data, JSON_PRETTY_PRINT));
        header('Location: index.php');
        exit;
    }
}

// Search for a book
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = strtolower($_GET['search']);
    $results = [];
    foreach ($data as $index => $book) {
        $bookTitle = strtolower($book['title']);
        // Check if the book title starts with the search term
        if (strpos($bookTitle, $searchTerm) === 0) {
            $results[] = ['title' => $book['title'], 'author' => $book['author'], 'index' => $index];
        }
    }
    if (empty($results)) {
        $noResultsMessage = "No results found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Library</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Book Entry</h1>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" required>
        <button type="submit">Add Book</button>
    </form>

    <h2>Search Books</h2>
    <form method="GET">
        <label for="search">Search:</label>
        <input type="text" name="search" id="search" placeholder="Search for books">
        <button type="submit">Search</button>
    </form>

    <?php if (isset($noResultsMessage)) {
        echo "<p>$noResultsMessage</p>";
    } ?>

    <h2>Book List</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Action</th>
        </tr>
        <?php if (isset($results)) {
            foreach ($results as $book) { ?>
                <tr>
                    <td><?= $book['title'] ?></td>
                    <td><?= $book['author'] ?></td>
                    <td>
                        <a href="?delete=<?= $book['index'] ?>">Delete</a>
                    </td>
                </tr>
            <?php }
        } else {
            foreach ($data as $index => $book) { ?>
                <tr>
                    <td><?= $book['title'] ?></td>
                    <td><?= $book['author'] ?></td>
                    <td>
                        <a href="?delete=<?= $index ?>">Delete</a>
                    </td>
                </tr>
            <?php }
        } ?>
    </table>
</body>
</html>
