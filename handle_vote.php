<?php
// handle_vote.php
include __DIR__ . '/.functions.php';
$conn = connectToDatabase();

session_start();
$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Please login to vote on threads.']);
    exit();
} else {
$user_id = $_SESSION['user_id'];
$thread_id = $_POST['thread_id'];
$vote_type = $_POST['vote_type'];

// Check if the user has already voted on this thread
$check_query = $conn->prepare("SELECT vote_type FROM user_votes WHERE user_id = ? AND thread_id = ?");
$check_query->bind_param("ii", $user_id, $thread_id);
$check_query->execute();
$result = $check_query->get_result();
$previous_vote = $result->fetch_assoc();

if ($previous_vote) {
    if ($previous_vote['vote_type'] == $vote_type) {
        // echo "You've already voted this way!";
    } else {
        // User is changing their vote. Update the vote type in the user_votes table.
        $update_vote_query = $conn->prepare("UPDATE user_votes SET vote_type = ? WHERE user_id = ? AND thread_id = ?");
        $update_vote_query->bind_param("sii", $vote_type, $user_id, $thread_id);
        $update_vote_query->execute();

        // Adjust the vote count on the thread table.
        $increment = ($vote_type == 'upvote') ? 2 : -2; // Since the user is changing their vote, the count will change by 2.
        $update_count_query = $conn->prepare("UPDATE thread SET vote_count = vote_count + ? WHERE thread_id = ?");
        $update_count_query->bind_param("ii", $increment, $thread_id);
        $update_count_query->execute();

        // echo "Your vote has been updated!";
    }
} else {
    // Insert the new vote into user_votes table
    $insert_query = $conn->prepare("INSERT INTO user_votes (user_id, thread_id, vote_type) VALUES (?, ?, ?)");
    $insert_query->bind_param("iis", $user_id, $thread_id, $vote_type);
    $insert_query->execute();

    // Update the vote count in thread table
    $increment = ($vote_type == 'upvote') ? 1 : -1;
    $update_query = $conn->prepare("UPDATE thread SET vote_count = vote_count + ? WHERE thread_id = ?");
    $update_query->bind_param("ii", $increment, $thread_id);
    $update_query->execute();

    // echo "Vote recorded!";
}

$query = $conn->prepare("SELECT vote_count FROM thread WHERE thread_id = ?");
$query->bind_param("i", $thread_id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$new_vote_count = $row['vote_count'];

echo json_encode(['newVoteCount' => $new_vote_count]);

$conn->close();
}
?>

