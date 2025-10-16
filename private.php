<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Private Chat</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Private Chat Room 🔒</h2>
<button onclick="showCreate()">Create Room</button>
<button onclick="showJoin()">Join Room</button>

<div id="createBox" style="display:none;">
    <input id="createName" placeholder="Your Name"><br>
    <input id="createSubject" placeholder="Chat Subject"><br>
    <button onclick="createRoom()">Create</button>
</div>

<div id="joinBox" style="display:none;">
    <input id="joinName" placeholder="Your Name"><br>
    <input id="joinCode" placeholder="Room Code"><br>
    <button onclick="joinRoom()">Join</button>
</div>

<script>
function showCreate(){
    document.getElementById('createBox').style.display='block';
    document.getElementById('joinBox').style.display='none';
}
function showJoin(){
    document.getElementById('joinBox').style.display='block';
    document.getElementById('createBox').style.display='none';
}

function createRoom(){
    const name = document.getElementById('createName').value;
    const subject = document.getElementById('createSubject').value;
    const code = Math.random().toString(36).substring(2,8).toUpperCase();
    localStorage.setItem('chatUser', name);
    window.location = `chat.php?room=${code}&subject=${encodeURIComponent(subject)}`;
}

function joinRoom(){
    const name = document.getElementById('joinName').value;
    const code = document.getElementById('joinCode').value.toUpperCase();
    localStorage.setItem('chatUser', name);
    window.location = `chat.php?room=${code}`;
}
</script>
</body>
</html>
