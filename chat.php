<?php
$room = $_GET['room'] ?? 'public';
$subject = $_GET['subject'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Room: <?=htmlspecialchars($room)?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="chat-wrapper">
    <aside id="sidebar">
      <div id="roomHeader">
        <h2 id="roomTitle">Room: <?=htmlspecialchars($room)?></h2>
        <div id="roomSubject"></div>
      </div>

      <section id="channels">
        <h4>Channels</h4>
        <div id="channelList"></div>
        <button id="addChannelBtn">+ Add Channel</button>
      </section>

      <section id="tools">
        <h4>Host Tools</h4>
        <button id="endRoomBtn">End Meeting</button>
      </section>

      <section id="members">
        <h4>Members</h4>
        <div id="memberList"></div>
      </section>
    </aside>

    <main class="chat-main">
      <div id="messages" aria-live="polite"></div>

      <div id="inputArea">
        <input id="input" placeholder="Type a message..." autocomplete="off" />
        <button id="send">Send</button>
      </div>
    </main>
  </div>

  <!-- global vars -->
  <script>
    window.room = "<?=htmlspecialchars($room)?>";
    window.user = localStorage.getItem('chatUser') || prompt("Enter your name:");
    if (!localStorage.getItem('chatUser')) localStorage.setItem('chatUser', window.user);
  </script>

  <script src="script.js"></script>
</body>
</html>
