@extends('layouts.app')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sales.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="content">
        <div class="d-flex justify-content-between mb-3">
            <h3>Chats</h3>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary chat-btn active" id="mailBtn" data-type="Mail">Mail</button>
            <button class="btn btn-outline-secondary chat-btn" id="whatsappBtn" data-type="WhatsApp">WhatsApp</button>
            <button class="btn btn-outline-secondary chat-btn" id="smsBtn" data-type="SMS">SMS</button>
            <button class="btn btn-outline-secondary chat-btn" id="liveChatBtn" data-type="Live Chat">Live Chat</button>
        </div>

        <div id="chatContent" class="mt-4">
            <!-- Default Mail Section -->
            <div class="p-3">
    <div class="border p-3 rounded" style="background: #fff;">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" href="#">Compose</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Inbox</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Draft</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Templates</a></li>
        </ul>
        <form id="sendMailForm">
            <div class="mt-3">
                <label>To:</label>
                <input type="email" class="form-control mb-2" name="to" required>

                <label>From:</label>
                <input type="email" class="form-control mb-2" name="from" value="{{ env('MAIL_FROM_ADDRESS') }}" readonly>

                <label>Subject:</label>
                <input type="text" class="form-control mb-2" name="subject" required>

                <label>Message:</label>
                <textarea class="form-control" name="message" rows="5" placeholder="Compose your message..." required></textarea>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
        </form>
    </div>
</div>

        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const chatContainer = document.getElementById("chatContent");
    const buttons = document.querySelectorAll(".chat-btn");
    const sendMailUrl = "{{ route('send.mail') }}"; // Blade route for sending mail

    function loadChatContent(type) {
        let content = "";
        switch (type) {
            case 'WhatsApp':
                content = `<div class='p-3'><p>Start your WhatsApp conversation here.</p></div>`;
                break;
            case 'Mail':
                content = `
                    <div class="p-3">
                        <div class="border p-3 rounded" style="background: #fff;">
                            <ul class="nav nav-tabs" id="emailTabs">
                                <li class="nav-item"><a class="nav-link active" data-folder="compose" href="#">Compose</a></li>
                                <li class="nav-item"><a class="nav-link" data-folder="inbox" href="#">Inbox</a></li>
                                <li class="nav-item"><a class="nav-link" data-folder="drafts" href="#">Draft</a></li>
                                <li class="nav-item"><a class="nav-link" data-folder="templates" href="#">Templates</a></li>
                            </ul>
                            <div id="emailContent" class="mt-3">
                                ${composeEmailForm()} <!-- Default: Compose Form -->
                            </div>
                        </div>
                    </div>`;
                break;
            case 'SMS':
                content = `
                    <div class='p-3'>
                        <label>Recipient:</label>
                        <input type="text" class="form-control mb-2">
                        <label>Message:</label>
                        <textarea class="form-control" rows="4" placeholder="Type your SMS message..."></textarea>
                        <button class="btn btn-primary mt-2">Send SMS</button>
                    </div>`;
                break;
            case 'Live Chat':
                content = `
                    <div class='p-3'>
                        <div class="border rounded p-2" style="height: 250px; overflow-y: auto; background: #f8f9fa;">
                            <p class="text-muted">Live chat messages will appear here.</p>
                        </div>
                        <input type="text" class="form-control mt-2" placeholder="Type a message...">
                        <button class="btn btn-primary mt-2">Send</button>
                    </div>`;
                break;
        }
        chatContainer.innerHTML = content;

        // Remove active class from all buttons
        buttons.forEach(btn => btn.classList.remove("active"));

        // Add active class to the clicked button
        document.querySelector(`[data-type="${type}"]`).classList.add("active");

        if (type === "Mail") {
            setupMailTabListeners();
            setupMailForm();
        }
    }

    function composeEmailForm() {
        return `
            <form id="sendMailForm">
                <div class="mt-3">
                    <label>To:</label>
                    <input type="email" class="form-control mb-2" name="to" required>

                    <label>From:</label>
                    <input type="email" class="form-control mb-2" name="from" value="{{ env('MAIL_FROM_ADDRESS') }}" readonly>

                    <label>Subject:</label>
                    <input type="text" class="form-control mb-2" name="subject" required>

                    <label>Message:</label>
                    <textarea class="form-control" name="message" rows="5" placeholder="Compose your message..." required></textarea>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </form>`;
    }

    function setupMailForm() {
        const mailForm = document.getElementById("sendMailForm");
        if (!mailForm) return;

        mailForm.addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(mailForm);

            fetch(sendMailUrl, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("✅ " + data.success);
                    mailForm.reset(); // Clear form on success
                } else {
                    alert("❌ Error: " + (data.message || "Something went wrong!"));
                }
            })
            .catch(error => {
                alert("❌ Error sending mail!");
            });
        });
    }

    function fetchEmails(folder) {
        fetch(`/public_html/emails/${folder}`)
            .then(response => response.json())
            .then(data => {
                let emailList = `<ul class="list-group">`;

                if (data.error) {
                    emailList += `<li class="list-group-item text-danger">${data.error}</li>`;
                } else {
                    data.forEach(email => {
                        emailList += `
                            <li class="list-group-item">
                                <strong>${email.subject}</strong><br>
                                From: ${email.from} <br>
                                Date: ${email.date} <br>
                                <p>${email.body.substring(0, 100)}...</p>
                            </li>`;
                    });
                }

                emailList += `</ul>`;
                document.getElementById("emailContent").innerHTML = emailList;
            })
            .catch(error => console.error("Error fetching emails:", error));
    }

    function setupMailTabListeners() {
        const emailTabs = document.getElementById("emailTabs");

        emailTabs.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default link behavior
            if (!event.target.classList.contains("nav-link")) return;

            document.querySelectorAll(".nav-link").forEach(tab => tab.classList.remove("active"));
            event.target.classList.add("active");

            let folder = event.target.getAttribute("data-folder");

            // Update content based on the selected tab
            if (folder === "compose") {
                document.getElementById("emailContent").innerHTML = composeEmailForm();
                setupMailForm();
            } else {
                document.getElementById("emailContent").innerHTML = "<p>Loading emails...</p>";
                fetchEmails(folder);
            }
        });
    }

    // Setup event listeners for chat navigation buttons
    buttons.forEach(button => {
        button.addEventListener("click", function () {
            loadChatContent(this.getAttribute("data-type"));
        });
    });

    // Load default chat content
    loadChatContent("Mail");
});


</script>

@endsection