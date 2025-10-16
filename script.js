// script.js - new robust client
const room = window.room;
const user = window.user;

const messagesDiv = document.getElementById('messages');
const memberListDiv = document.getElementById('memberList');
const channelListDiv = document.getElementById('channelList');
const roomTitle = document.getElementById('roomTitle');
const roomSubject = document.getElementById('roomSubject');

const input = document.getElementById('input');
const sendBtn = document.getElementById('send');
const addChannelBtn = document.getElementById('addChannelBtn');
const endRoomBtn = document.getElementById('endRoomBtn');

let currentChannel = 'general';
let channels = []; // array of channel names
let isHost = false;

// Initialize
roomTitle.textContent = `Room: ${room}`;
init();

async function init(){
  // Let server know this user is active (members heartbeat)
  await fetch(`api/get_members.php?room=${encodeURIComponent(room)}&user=${encodeURIComponent(user)}`);

  // get full room state (channels, messages, subject, host)
  const res = await fetch(`api/get_room_state.php?room=${encodeURIComponent(room)}`);
  const data = await res.json();

  if (data.exists === false) {
    // room doesn't exist -> if public -> create minimal public room; else ask host to create
    if (room === 'public') {
      await fetch(`api/create_room.php?subject=${encodeURIComponent('Public Room')}&host=${encodeURIComponent('system')}`);
      // re-fetch
      const r2 = await fetch(`api/get_room_state.php?room=${encodeURIComponent(room)}`);
      Object.assign(data, await r2.json());
    } else {
      alert('Room not found. You can create a new room from the landing page.');
      window.location.href = 'index.php';
      return;
    }
  }

  // set subject & host
  roomSubject.textContent = data.subject || '';

  // host check
  isHost = (data.host === user);
  if (!isHost) endRoomBtn.style.display = 'none';

  // populate channels
  channels = data.channels || ['general'];
  renderChannels();

  // show recent messages for currentChannel
  if (channels.includes('general')) currentChannel = 'general';
  else currentChannel = channels[0];
  loadMessages();

  // polling
  setInterval(loadMessages, 1000);
  setInterval(() => fetch(`api/get_members.php?room=${encodeURIComponent(room)}&user=${encodeURIComponent(user)}`), 7000);
  setInterval(loadMembers, 2000);

  // events
  sendBtn.addEventListener('click', sendMessage);
  input.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });
  addChannelBtn.addEventListener('click', createChannelPrompt);
  endRoomBtn.addEventListener('click', endRoom);
  loadMembers();
}
init(); // call init to start
function renderChannels(){
  channelListDiv.innerHTML = '';
  channels.forEach(ch => {
    const btn = document.createElement('button');
    btn.className = 'channel-btn';
    if (ch === currentChannel) btn.classList.add('active');
    btn.textContent = '# ' + ch;
    btn.onclick = () => {
      currentChannel = ch;
      // mark active class
      document.querySelectorAll('.channel-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadMessages();
    };
    channelListDiv.appendChild(btn);
  });
}
renderChannels(); // initial render
async function createChannelPrompt(){
  const name = prompt('Enter new channel name (no spaces):').trim();
  if (!name) return;
  await fetch(`api/create_channel.php?room=${encodeURIComponent(room)}&channel=${encodeURIComponent(name)}&user=${encodeURIComponent(user)}`);
  // refresh channels from server
  const res = await fetch(`api/get_channels.php?room=${encodeURIComponent(room)}`);
  channels = await res.json();
  renderChannels();
}
createChannelPrompt();  // for testing, auto prompt on load
async function loadMessages(){
  const res = await fetch(`api/get_messages.php?room=${encodeURIComponent(room)}&channel=${encodeURIComponent(currentChannel)}`);
  const data = await res.json();
  messagesDiv.innerHTML = '';
  data.forEach(m => {
    const div = document.createElement('div');
    div.className = 'message';
    const name = document.createElement('div');
    name.className = 'name';
    name.textContent = m.user;
    const text = document.createElement('div');
    text.className = 'text';

    // parse links and copy button
    const linkRegex = /(https?:\/\/[^\s]+)/g;
    let parts = m.text.split(linkRegex);
    parts.forEach(p => {
      if (linkRegex.test(p)) {
        const a = document.createElement('a');
        a.href = p; a.textContent = p; a.target = '_blank';
        text.appendChild(a);
        const c = document.createElement('button');
        c.className = 'copy-btn';
        c.textContent = 'Copy';
        c.onclick = () => navigator.clipboard.writeText(p);
        text.appendChild(c);
      } else {
        text.appendChild(document.createTextNode(p));
      }
    });

    div.appendChild(name);
    div.appendChild(text);
    messagesDiv.appendChild(div);
  });
  messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

async function loadMembers(){
  const res = await fetch(`api/get_members_list.php?room=${encodeURIComponent(room)}`);
  const arr = await res.json();
  memberListDiv.innerHTML = '';
  arr.forEach(u => {
    const d = document.createElement('div');
    d.className = 'member';
    d.textContent = u;
    memberListDiv.appendChild(d);
  });
}
loadMembers();
async function sendMessage(){
  const txt = input.value.trim();
  if (!txt) return;
  await fetch(`api/send_message.php?room=${encodeURIComponent(room)}&channel=${encodeURIComponent(currentChannel)}&user=${encodeURIComponent(user)}&text=${encodeURIComponent(txt)}`);
  input.value = '';
  loadMessages();
}
sendMessage();
async function endRoom(){
  if (!confirm('End this meeting? This will delete the room and all messages.')) return;
  const res = await fetch('api/end_room.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `room=${encodeURIComponent(room)}&user=${encodeURIComponent(user)}`
  });

  const json = await res.json();
  if (json.success) {
    alert('Room ended.');
    window.location.href = 'index.php';
  } else {
    alert('Unable to end room: ' + (json.error || 'unknown'));
  }
}
endRoom();
// End of script.js
