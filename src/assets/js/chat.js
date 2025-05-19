const idSession = get(".id_session");
const AI = document.getElementById("ai").value;
let CHATID = document.getElementById("chatid").value;
let AIICON = document.getElementById("aiicon").value;
if (AI === '201') {
    CHATID = 'multilang';
}

const lang = window.sessionStorage.getItem('lang') || 'en';
//idSession.textContent = CHATID;

getHistory();

const msgerSendBtn = get(".message-submit");
// Function to delete chat history records for a user ID using the API
function deleteChatHistory(userId) {
    if (!confirm("Are you sure? Your Session and History will delete for good.")) {
        return false
    }

    fetch('/a/chat.php?chatid=' + CHATID, {
        method: 'DELETE',
        headers: {'Content-Type': 'application/json'}
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error deleting chat history: ' + response.statusText);
            }
            deleteThisCookie()
            location.reload(); // Reload the page to update the chat history table
        })
        .catch(error => console.error(error));
}

function getHistory() {
    var formData = new FormData();
    formData.append('chatid', CHATID);
    fetch('/a/chat.php', {method: 'POST', body: formData})
        .then(response => response.json())
        .then(chatHistory => {
            for (const row of chatHistory) {
                console.log(row);
                if (row.human) $('<div class="message message-personal">' + row.human + '</div>').appendTo($('.mCSB_container')).addClass('new');
                //if (row.ai) $('<div class="message new"><figure class="avatar"><img src="'+AIICON+'" /></figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                if (row.ai) {
                    if (AI == '201') {
                        $('<div class="message new"><figure class="avatar" style="padding:6;background-color:white;color:black">' + row.firstname.charAt(0) + row.lastname.charAt(0) + '</figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                        $('<div class="timestamp" style="width:450px">' + row.firstname + ' ' + row.lastname + ': ' + row.cdate + '</div>').appendTo($('.message:last'));
                    } else {
                        $('<div class="message new"><figure class="avatar"><img src="'+AIICON+'" /></figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                    }
                }
            }
        })
        .catch(error => {
            console.error(error)
        });
    if (AI == '201') {
        checkForHistoryUpdates();
    }
}

function checkForHistoryUpdates() {
    var formData = new FormData();
    formData.append('chatid', CHATID);
    setInterval(function() {
        fetch('/a/chat-event-stream.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(chatHistory => {
                for (const row of chatHistory) {
                    debugger;
                    if (row.human) $('<div class="message message-personal">' + row.human + '</div>').appendTo($('.mCSB_container')).addClass('new');
                    //if (row.ai) $('<div class="message new"><figure class="avatar"><img src="'+AIICON+'" /></figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                    if (row.ai) {
                        if (AI == '201') {
                            $('<div class="message new"><figure class="avatar" style="padding:6;background-color:white;color:black">' + row.firstname.charAt(0) + row.lastname.charAt(0) + '</figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                            $('<div class="timestamp" style="width:450px">' + row.firstname + ' ' + row.lastname + ': ' + row.cdate + '</div>').appendTo($('.message:last'));
                        } else {
                            $('<div class="message new"><figure class="avatar"><img src="'+AIICON+'" /></figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                        }
                    }
                }
            })
            .catch(error => {
                console.error(error)
            });
    }, 4000);
}

function checkForNewMessages() {
    const eventSource = new EventSource(`/a/chat-event-stream.php`);

    eventSource.onmessage = function (e) {
        if (e.data == "[DONE]") {
            msgerSendBtn.disabled = false
            eventSource.close();
        } else {
            let newHistory = JSON.parse(e.data);
            console.log(newHistory);
            for (const row of newHistory) {
                if (row.human) $('<div class="message message-personal">' + row.human + '</div>').appendTo($('.mCSB_container')).addClass('new');
                //if (row.ai) $('<div class="message new"><figure class="avatar"><img src="'+AIICON+'" /></figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                if (row.ai) {
                    $('<div class="message new"><figure class="avatar" style="padding:6;background-color:white;color:black">' + row.firstname.charAt(0) + row.lastname.charAt(0) + '</figure>' + row.ai + '</div>').appendTo($('.mCSB_container')).addClass('new');
                    $('<div class="timestamp" style="width:450px">' + row.firstname + ' ' + row.lastname + ': ' + row.cdate + '</div>').appendTo($('.message:last'));
                }
            }
        }
    };
    eventSource.onerror = function (e) {
        msgerSendBtn.disabled = false
        console.log(e);
        eventSource.close();
    };
}

function doAction(url) {
    var formData = new FormData();
    let dUrl = url.replace('https://your-domain.de', window.location.origin);
    if (dUrl.indexOf('\n') > -1) {
        dUrl = dUrl.substring(0, dUrl.indexOf('\n'));
    }
    if (dUrl.indexOf('api') == -1) {
        if (dUrl.indexOf('datenschutz') !== -1
          || dUrl.indexOf('satzung') !== -1
          || dUrl.indexOf('vertrag') !== -1
            || dUrl.indexOf('ai_d') !== -1) {
            const responseIcon = '/assets/images/correct.png';
            $('<div class="message new"><figure class="avatar"><img src="' + responseIcon + '" /></figure><a href="' + dUrl + '" target="_blank" style="color:white">' + dUrl + '</a></div>').appendTo($('.mCSB_container')).addClass('new');
            window.open(dUrl);
        }
    } else {
        console.log('Fetching ' + dUrl);
        formData.append('chatid', CHATID);
        fetch(dUrl, {method: 'POST', body: formData})
            .then(response => response.json())
            .then(response => {
                const responseIcon = response.status == 'ok' ? '/assets/images/correct.png' : '/assets/images/cross.png';
                $('<div class="message new"><figure class="avatar"><img src="' + responseIcon + '" /></figure>' + response.response + '</div>').appendTo($('.mCSB_container')).addClass('new');
            })
            .catch(error => {
                //$('<div class="message new"><figure class="avatar"><img src="/assets/images/cross.png" /></figure>Aktion fehlgeschlagen</div>').appendTo($('.mCSB_container')).addClass('new');
                console.error(error)
            });
    }
}

function sendMsgT() {
    const msg = $('.message-input').val();
    if ($.trim(msg) == '') {
        return false;
    }

    if (msg=='Hello again') {
        deleteThisCookie()
        location.reload();
    }

    if (msg=='Forget this conversation') {
        deleteChatHistory(CHATID);
        return false;
    }

    msgerSendBtn.disabled = true
    var formData = new FormData();
    formData.append('msg', msg);
    formData.append('chatid', CHATID);
    formData.append('ai', AI);
    fetch('/a/chat-send-message.php', {method: 'POST', body: formData})
        .then(response => response.json())
        .then(data => {
            let uuidT = uuidv4()
            // transparent
            if ($('.message-input').val() != '') {
                return false;
            }
            const msgerChatT = get(".mCSB_container");

            if (AI !== "200" && AI !== "201") {
                msgerChatT.insertAdjacentHTML("beforeend", '<div class="message loading new" id="'+uuidT+'"><figure class="avatar"><img src="'+AIICON+'" /></figure><span></span></div>');

                updateScrollbar();

                const eventSource = new EventSource(`/a/chat-event-stream.php?chat_history_id=${data.id}&id=${encodeURIComponent(CHATID)}&ai=${encodeURIComponent(AI)}`);

                const divT = document.getElementById(uuidT);

                let actionMode = false;

                let url = '';

                eventSource.onmessage = function (e) {
                    if (e.data == "[DONE]") {
                        msgerSendBtn.disabled = false
                        if (url) {
                            url = url.substring(6);
                            console.log('Action: ' + url);
                            doAction(url);
                        }
                        eventSource.close();
                    } else {
                        console.log(e.data);
                        let dataParsed = {};
                        try {
                            dataParsed = JSON.parse(e.data);
                        } catch (e) {
                            console.log('parse issue');
                        }
                        if (dataParsed.error) {
                            console.log('error: ' + dataParsed.error);
                            //divT.innerHTML += dataParsed.error.replace(/(?:\r\n|\r|\n)/g, '<br>');
                            $('#'+uuidT).hide();
                            $('<div class="message new"><figure class="avatar"><img src="/assets/images/cross.png" /></figure>' + dataParsed.error + '</div>').appendTo($('.mCSB_container')).addClass('new');
                            setDate();
                            updateScrollbar();
                            msgerSendBtn.disabled = false;
                            eventSource.close();
                            return;
                        }

                        if (dataParsed.choices !== undefined) {
                            let txt = dataParsed.choices[0].delta.content;
                            if (txt !== undefined) {
                                if (txt == "[" || actionMode) {
                                    actionMode = true;
                                    url += txt
                                    //console.log('url: ' + url);
                                    $('.message.loading').removeClass('loading');
                                    setDate();
                                    updateScrollbar();
                                    if (url.length === 5 && !url.startsWith('[URL]')) {
                                        actionMode = false;
                                        error.log('wrong URL detected: ' + url);
                                        url = '';
                                        divT.innerHTML += url.replace(/(?:\r\n|\r|\n)/g, '<br>');
                                    }
                                } else {
                                    $('.message.loading').removeClass('loading');
                                    setDate();
                                    updateScrollbar();
                                    divT.innerHTML += txt.replace(/(?:\r\n|\r|\n)/g, '<br>');
                                }
                            }
                        }
                    }
                };
                eventSource.onerror = function (e) {
                    msgerSendBtn.disabled = false
                    console.log(e);
                    eventSource.close();
                };
            } else {
                $('.message.loading').removeClass('loading');
            }
        })
        .catch(error => console.error(error));

}

// Utils
function get(selector, root = document) {
    return root.querySelector(selector);
}

function formatDate(date) {
    const h = "0" + date.getHours();
    const m = "0" + date.getMinutes();

    return `${h.slice(-2)}:${m.slice(-2)}`;
}

function random(min, max) {
    return Math.floor(Math.random() * (max - min) + min);
}

function deleteThisCookie() {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        if ('chatid' === name.trim()) {
            document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
        }
    }
}

function deleteAllCookies() {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}
