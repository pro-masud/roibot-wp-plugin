// Function to dynamically load Firebase SDKs
function loadFirebaseScripts() {
  const scripts = [
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js",
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js",
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-database-compat.js",
    "https://www.gstatic.com/firebasejs/10.12.2/firebase-storage-compat.js",
  ];

  let promises = scripts.map((src) => {
    return new Promise((resolve, reject) => {
      let script = document.createElement("script");
      script.src = src;
      script.onload = () => resolve(src);
      script.onerror = () => reject(new Error(`Failed to load script ${src}`));
      document.head.appendChild(script);
    });
  });

  return Promise.all(promises);
}

// Initialize Firebase
function initializeFirebase() {
  const firebaseConfigDev = {
    apiKey: "AIzaSyDeZjyhICotV25eR0IV22dhvhJ_8C9MFjs",
    authDomain: "roi-414507.firebaseapp.com",
    projectId: "roi-414507",
    storageBucket: "roi-414507.appspot.com",
    messagingSenderId: "556170493308",
    appId: "1:556170493308:web:658c88472ea909499db8f9",
    measurementId: "G-8B4L6DKQRK",
    databaseURL: "https://roi-414507-dev-rtdb.firebaseio.com/",
  };

  // Check if Firebase is loaded
  if (typeof firebase === "undefined") {
    throw new Error("Firebase is not loaded");
  }

  // Initialize Firebase
  const app = firebase.initializeApp(firebaseConfigDev);
  const auth = firebase.auth();
  const database = firebase.database();
  const storage = firebase.storage();

  return { app, auth, database, storage };
}

// Function to perform login using Firebase Authentication
function doLogin(auth) {
  auth
    .signInWithEmailAndPassword("pukarpukar@gmail.com", "aab351")
    .then((userCredential) => {
      // User successfully signed in
      const user = userCredential.user;
      console.log("User signed in:", user);
      // Now you can initialize the database connection
      initializeDatabase();
    })
    .catch((error) => {
      // Error occurred during sign-in
      console.error("Error signing in:", error);
    });

  auth.onAuthStateChanged((user) => {
    if (user) {
      console.log("Firebase auth state changed:", user);
      // If user is already signed in, initialize the database
      initializeDatabase();
    } else {
      console.log("User not signed in");
    }
  });
}

// Function to initialize the database connection
function initializeDatabase() {
  console.log("Database initialized successfully.");
  // You can now perform database operations here
}

// Load Firebase scripts and initialize Firebase
loadFirebaseScripts()
  .then(() => {
    const { auth } = initializeFirebase();
    // Perform login after initializing Firebase
    doLogin(auth);
  })
  .catch((error) => console.error("Error loading Firebase scripts:", error));

(function ($) {
  "use strict";
  // Event listener for beforeunload event
  window.addEventListener("beforeunload", function () {
    // Remove user token from local storage
    localStorage.removeItem("userToken");
  });

  $(document).ready(function () {
    // Show the help-text and chatbot-toggler initially
    $(".help-text").removeClass("hidden");
    $(".chatbot-toggler").removeClass("hidden");

    // By default, select the "Chat" tab and show the textarea field
    $("#imageUploadInput").addClass("hidden");
    $(".chatbot-toggler").removeClass("hidden");
    $(".message-form").removeClass("hidden");

    // Initialize a variable to store the user token
    let userToken = null;

    // Check if user token is already in local storage
    if (localStorage.getItem("userToken")) {
      userToken = localStorage.getItem("userToken");
    }

    // Set a flag in session storage to detect normal reloads
    sessionStorage.setItem("normalReload", "true");

    // Event listener for the toggler
    $(".chatbot-toggler").click(function () {
      $(".show-chatbot").removeClass("hidden");
      $(".chatbot-toggler").addClass("hidden");
      // Hit the API to get company info when the chatbot toggler is clicked
      $.ajax({
        url: "https://devapi.roilevelup.com/company_info",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ company_name: "ROI" }),
        success: function (response) {
          // Handle successful response from the API
          console.log("Company Info:", response);
        },
        error: function (xhr, status, error) {
          // Handle error response from the API
          console.error("Error:", error);
        },
      });
    });

    // Event listener for the close button
    $(".close-btn").click(function () {
      $(".show-chatbot").addClass("hidden");
      $(".chatbot-toggler").removeClass("hidden");
    });

    // Event listener for the help text close button
    $(".close-icon").click(function () {
      $(".help-text").addClass("hidden");
    });

    // Event listener for the single-chat tab
    // $(".single-chat").click(function () {
    //   if (userToken) {
    //     // Show message form
    //     //$(".message-form").removeClass("hidden");
    //     // Hide POC list
    //     $(".chatlist").empty();
    //   } else {
    //     // Display a message or handle case where user token is not available
    //     alert(
    //       "You need to send a message or provide contact information first."
    //     );
    //   }
    // });

    // Event listener for the send button in the chat form
    $(".send-btn").click(function () {
      sendMessage();
      // Clear the form after submission
      resetForm();
    });

    // Variable to track if the invitation has been sent
    var invitationSent = false;

    $(".form-input textarea")
      .off("keydown")
      .on("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          sendMessage();
          resetForm();
        }
      });
    $(".form-input textarea").on("input", function () {
      this.style.height = "auto";
      this.style.height = Math.min(this.scrollHeight, 160) + "px";
    });
    // Function to reset the form fields
    function resetForm() {
      $(".form-input textarea").val(""); // Clear the textarea
    }

    // Function to send a message
    function sendMessage() {
      var userMessage = $(".form-input textarea").val().trim();
      if (!userMessage) return;

      // If the user typed contact info, send invitation ONCE, but never block normal chat.
      var contactInfo = parseContactInfo(userMessage);
      if (contactInfo && !invitationSent) {
        sendInvitation(contactInfo);
      }

      // Always send the normal message
      sendNormalMessage(userMessage);

      // Clear any error UI
      $(".error-message").text("").hide();
    }
    function parseContactInfo(message) {
      // Regular expressions to match contact number and email
      var contactRegex = /\b\d{10}\b/;
      var emailRegex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/;
      // Match contact number
      var contactMatch = message.match(contactRegex);
      if (contactMatch) {
        return { contact: contactMatch[0] };
      }
      // Match email
      var emailMatch = message.match(emailRegex);
      if (emailMatch) {
        return { email: emailMatch[0] };
      }
      return null; // No contact info found
    }

    // Function to send invitation to the API
    function sendInvitation(contactInfo) {
      // Add additional parameters and send invitation
      var invitationData = {
        fname: "", // Can be empty but add in code
        relationship_id: 5, // Required
        user_type: "individual", // Required
        company_name: "ROI", // Required
        email: "", // Can be empty but add in code
        ...contactInfo, // Add contact information obtained from user message
      };
      $.ajax({
        url: "https://devapi.roilevelup.com/send_invitation_from_landingpage",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(invitationData),
        success: function (response) {
          // Handle successful response from the API
          console.log("Invitation Sent:", response);

          if (response.message === "This contact already exist.") {
            // Check if contact exists in your database
            checkAndSaveContact(invitationData, response);
          } else {
            // Proceed with normal invitation process
            saveContactToDatabase(invitationData, response);
          }

          // Extract userId from the response
          var userId = response.added_contacts[0].id;

          // Store the user ID in local storage
          if (userId) {
            localStorage.setItem("userId", userId);
            console.log("User ID stored in local storage:", userId);
          }

          // Clear any existing error message
          $(".error-message").text("").hide();

          // Get and display POCs after sending invitation
          if (response.user_token) {
            userToken = response.user_token; // Store the user token
            localStorage.setItem("userToken", userToken); // Save user token in local storage
            getPOCsAndDisplay(userToken);
          }
        },
        error: function (xhr, status, error) {
          // Handle error response from the API
          console.error("Error:", error);
          // Show error message to the user or handle as needed
          $(".error-message")
            .text("Error sending invitation. Please try again.")
            .show();
        },
      });
    }

    // Event listener for the chat list tab
    $(".chat-list-tab").click(function () {
      // Check if user token is available
      if (userToken) {
        // Hide message form
        $(".message-form").addClass("hidden");
        // Show POC list
        getPOCsAndDisplay(userToken);
      } else {
        // Display a message or handle case where user token is not available
        alert(
          "You need to send a message or provide contact information first."
        );
      }
    });

    // Global variable to store the profile images
    var storedProfileImgs = [];

    function getPOCsAndDisplay(userToken) {
      // Hide POC list
      $(".chatlist").empty();
      // Fetch and display POCs when the "Chat List" tab is clicked
      $.ajax({
        url: "https://devapi.roilevelup.com/list_of_all_pocs_of_company",
        type: "POST",
        contentType: "application/json",
        headers: {
          Authorization: "Bearer " + userToken, // Replace userToken with the actual user token
        },
        data: JSON.stringify({
          company_name: "ROI",
        }),
        success: function (response) {
          console.log("POCs:", response);
          displayPOCs(response.data, userToken);
          $(".message-form").addClass("hidden");
        },
        error: function (xhr, status, error) {
          console.error("Error fetching POCs:", error);
        },
      });
    }

    function displayPOCs(pocs, userToken) {
      $(".chatlist").empty();
      $("#chat-list").addClass("poclist");

      var containerDiv = $("<div class='message-items-container'></div>");

      storedProfileImgs = pocs.slice(0, 2).map(function (poc) {
        return poc.user_detail.profile_img
          ? poc.user_detail.profile_img
          : "https://example.com/default-profile-image.jpg";
      });

      console.log("Stored Profile Images:", storedProfileImgs);

      // Update profile images in header
      updateHeaderProfileImages();

      pocs.forEach(function (poc) {
        var firstName = poc.user_detail.first_name;
        var profileImg = poc.user_detail.profile_img
          ? poc.user_detail.profile_img
          : "https://example.com/default-profile-image.jpg";

        var messageItem = $(
          '<div class="message-item bot"><div class="msg-content"><img src="' +
            profileImg +
            '" alt="' +
            firstName +
            '"> <p>' +
            firstName +
            "</p></div></div>"
        );

        messageItem.click(function () {
          handlePocClick(poc.user_detail.id, userToken, firstName, profileImg);
        });

        containerDiv.append(messageItem);
      });

      var __li = $('<li class="message-item poclist"></li>').append(
        containerDiv
      );
      $(".chatlist").append(__li);
    }

    function handlePocClick(pocUserId, userToken, pocName, pocProfileImg) {
      $.ajax({
        url: "https://devapi.roilevelup.com/check_chat_room",
        type: "POST",
        contentType: "application/json",
        headers: {
          Authorization: "Bearer " + userToken,
        },
        data: JSON.stringify({
          user_id: pocUserId,
          company_name: "ROI",
          chat_type: "live_chat",
        }),
        success: function (response) {
          if (response.exist) {
            // Store the chat_room_name in local storage
            localStorage.setItem("chat_room_name", response.chat_room_name);
            localStorage.setItem("chat_room_id", response.chat_room_id);
            openMessageInterface(
              response.chat_detail.chat_room_id,
              pocName,
              userToken,
              pocProfileImg
            );
          } else {
            createChatRoom(pocUserId, userToken, pocName);
          }
        },
        error: function (xhr, status, error) {
          console.error("Error checking chat room:", error);
        },
      });
    }

    function createChatRoom(pocUserId, userToken, pocName) {
      var chatRoomName = generateRandomString(); // Generate a random string for chat room name
      $.ajax({
        url: "https://devapi.roilevelup.com/create_chat_room",
        type: "POST",
        contentType: "application/json",
        headers: {
          Authorization: "Bearer " + userToken,
        },
        data: JSON.stringify({
          chat_room_name: chatRoomName,
          chat_room_type: "single",
          type: "live_chat",
          user_id: pocUserId,
          company_name: "ROI",
        }),
        success: function (response) {
          // Chat room created, open message interface and display chat history
          openMessageInterface(response.chat_room_id, pocName, userToken);
        },
        error: function (xhr, status, error) {
          console.error("Error creating chat room:", error);
        },
      });
    }

    function updateHeaderProfileImages() {
      // Check if the first image element exists and update the src
      var firstImage = $(".chatbot-header .avtar img:first-child");
      var secondImage = $(".chatbot-header .avtar img:nth-child(2)");

      if (firstImage.length) {
        console.log("Updating first image src");
        firstImage.attr(
          "src",
          storedProfileImgs[0] ||
            "https://example.com/default-profile-image.jpg"
        );
      } else {
        console.error("First image element not found!");
      }

      if (secondImage.length) {
        console.log("Updating second image src");
        secondImage.attr(
          "src",
          storedProfileImgs[1] ||
            "https://example.com/default-profile-image.jpg"
        );
      } else {
        console.log("Second image element not found, creating it.");
        // Dynamically create the second image element and append it
        var newSecondImage = $("<img>").attr(
          "src",
          storedProfileImgs[1] ||
            "https://example.com/default-profile-image.jpg"
        );
        $(".chatbot-header .avtar").append(newSecondImage);
      }
    }

    function openMessageInterface(
      chatRoomId,
      pocName,
      userToken,
      pocProfileImg
    ) {
      console.log($(".chatbot-header .avtar img").length);

      $(".chatbot-header .avtar img:first-child").attr("src", pocProfileImg);
      $(".chatbot-header .avtar img:last-child").remove();

      $(".chatlist").empty();

      var backButton = $(
        '<span class="back-btn material-symbols-outlined"><svg width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m9 14l-4-4l4-4"/><path d="M5 10h11a4 4 0 1 1 0 8h-1"/></g></svg></span>'
      );
      $(".chatbot-header").prepend(backButton);

      backButton.click(function () {
        console.log("Back button clicked. Restoring profile images.");
        getPOCsAndDisplay(userToken);
        $(".message-form").addClass("hidden");
        backButton.remove();

        // Update the profile images in header after clicking back
        updateHeaderProfileImages();
      });

      $("#imageUploadInput").removeClass("hidden");
      var chatHeader = $(".chatlist").append(chatHeader);
      $(".message-form").removeClass("hidden");

      function handleSendMessage() {
        var message = $(".form-input textarea").val().trim();
        if (message) {
          sendMessageToChatRoom(chatRoomId, message, userToken);
          $(".form-input textarea").val("");
        }
      }

      $(".send-btn").off("click").on("click", handleSendMessage);
      $(".form-input textarea")
        .off("keydown")
        .on("keydown", function (event) {
          if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault();
            handleSendMessage();
          }
        });

      $(".form-input textarea")
        .off("keypress")
        .on("keypress", function (e) {
          if (e.which === 13) {
            e.preventDefault();
            var message = $(".form-input textarea").val().trim();
            if (message) {
              sendMessageToChatRoom(chatRoomId, message, userToken);
            }
          }
        });

      fetchAndDisplayMessages(chatRoomId);
    }

    // Function to fetch and display all messages from Firebase
    function fetchAndDisplayMessages(chatRoomId) {
      // Retrieve chat_room_name from local storage
      const chatRoomName = localStorage.getItem("chat_room_name");
      if (!chatRoomName) {
        console.error("Chat room name not found in local storage.");
        return;
      }

      // Reference to the chat room's messages in the Firebase database
      const firebaseRef = firebase
        .database()
        .ref("chatrooms/chats/" + chatRoomName);

      // Fetch messages from Firebase
      firebaseRef
        .once("value")
        .then((snapshot) => {
          // Iterate through each date node
          snapshot.forEach((dateSnapshot) => {
            // Get date key (YYYY-MM-DD)
            const dateKey = dateSnapshot.key;
            // Iterate through each message under the date
            dateSnapshot.forEach((messageSnapshot) => {
              // Get message data
              const messageData = messageSnapshot.val();
              // Display message in the chat interface
              displayMessage(
                messageData.message,
                messageData.sent_by,
                messageData.picture_url
              );
            });
          });
        })
        .catch((error) => {
          console.error("Error fetching messages from Firebase:", error);
        });
    }

    // Function to format date for chat
    function getDateFormatForChat(timestamp) {
      const date = new Date(timestamp);
      const year = date.getFullYear();
      const month = ("0" + (date.getMonth() + 1)).slice(-2);
      const day = ("0" + date.getDate()).slice(-2);
      return year + "-" + month + "-" + day; // Format: YYYY-MM-DD
    }

    // Event listener for image upload input
    $("#imageUploadInput").change(function (event) {
      const file = event.target.files[0];
      const chatRoomId = localStorage.getItem("chat_room_id"); // Define chatRoomId here or fetch it from somewhere
      const message = $("#messageInput").val(); // Get the message from an input field or any other source
      const userToken = localStorage.getItem("userToken"); // Define userToken here or fetch it from somewhere

      if (file && message.trim() !== "") {
        // Upload image to Firebase Storage if file exists and message is not empty
        uploadImageToStorage(file, chatRoomId, message, userToken);
      } else if (!file && message.trim() !== "") {
        // If no image but message exists, send text message only
        sendMessageToChatRoom(chatRoomId, message, userToken, "");
      } else if (file && message.trim() === "") {
        // If image but no message, send image only
        uploadImageToStorage(file, chatRoomId, "", userToken);
      } else {
        console.error("No message or image provided.");
      }
    });

    // Function to upload image to Firebase Storage
    function uploadImageToStorage(imageFile, chatRoomId, message, userToken) {
      const fileName =
        "image_" + Math.floor(Date.now() / 1000).toString() + ".png";
      const storageRef = firebase.storage().ref("images/" + fileName);

      const uploadTask = storageRef.put(imageFile);

      uploadTask.on(
        "state_changed",
        function (snapshot) {
          const progress =
            (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
          console.log("Upload is " + progress + "% done");
        },
        function (error) {
          console.error("Error uploading image:", error);
        },
        function () {
          uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {
            console.log("Image uploaded successfully. URL:", downloadURL);
            // Call sendMessageToChatRoom here and pass the chatRoomId, message, and userToken parameters
            sendMessageToChatRoom(chatRoomId, message, userToken, downloadURL);
          });
        }
      );
    }

    // Function to send a message to a chat room
    function sendMessageToChatRoom(chatRoomId, message, userToken, imageUrl) {
      // Retrieve chat_room_name from local storage
      const chatRoomName = localStorage.getItem("chat_room_name");
      if (!chatRoomName) {
        console.error("Chat room name not found in local storage.");
        return;
      }

      // Function to sanitize message object for Firebase
      function sanitizeMessageObject(msg, sentBy, imageUrl) {
        return {
          message: msg.message || "", // Use message from API response
          emoji: msg.emoji || "",
          accomplishment_data: "", // Empty fields
          appointment_data: "", // Empty fields
          group_chat_data: "", // Empty fields
          event_data: "", // Empty fields
          session_data: "", // Empty fields
          opportunity_data: "", // Empty fields
          network_data: "", // Empty fields
          picture_url: imageUrl || "", // Image URL if available
          document_url: "", // Empty fields
          video_url: "", // Empty fields
          type: "message",
          isPrivate: false,
          privateChatData: {
            first_name: "", // Empty fields
            last_name: "", // Empty fields
            profile_img: "", // Empty fields
            sent_to: "", // Empty fields
          },
          sent_by: sentBy || "unknown", // Use sent_by from API response, default to 'unknown'
          send_date: new Date().getTime(), // Use current timestamp
        };
      }

      // Log the message text before sending
      console.log("Message Text:", message);

      // Function to handle the entire message sending process
      function handleMessageSending(imageUrl) {
        // Send message to API
        $.ajax({
          url: "https://devapi.roilevelup.com/add_last_message",
          type: "POST",
          contentType: "application/json",
          headers: {
            Authorization: "Bearer " + userToken,
          },
          data: JSON.stringify({
            chat_room_id: chatRoomId,
            last_message: message,
            company_name: "ROI",
          }),
          success: function (response) {
            console.log("Response from API:", response);

            // Check if the userId is stored in local storage
            const userId = localStorage.getItem("userId");
            if (!userId) {
              console.error("User ID not found in local storage.");
              return;
            }

            // Extract sent_by value from response
            const sentBy = userId;

            // Prepare message object for Firebase
            const firebaseMessage = sanitizeMessageObject(
              {
                message: message,
                send_date: new Date().getTime(),
              },
              sentBy,
              imageUrl
            );

            // Log the firebase message object before sending
            console.log("Firebase Message Object:", firebaseMessage);

            // Save message to Firebase database
            const firebaseRef = firebase
              .database()
              .ref(
                "chatrooms/chats/" +
                  chatRoomName +
                  "/" +
                  getDateFormatForChat(Date.now())
              );
            firebaseRef
              .push(firebaseMessage)
              .then(() => {
                console.log("Message saved to Firebase successfully.");
                console.log("Pushed to Firebase:", firebaseMessage); // Log the pushed message

                // Display the sent message in the chat interface
                displayMessage(message, userId, imageUrl); // Display the message text with image if available
              })
              .catch((error) => {
                console.error("Error saving message to Firebase:", error);
              });
          },
          error: function (xhr, status, error) {
            console.error("Error sending message:", error);
          },
        });
      }

      // Check if an image is selected
      if (imageUrl) {
        handleMessageSending(imageUrl);
      } else {
        handleMessageSending(""); // Proceed without image URL
      }
    }

    function generateRandomString() {
      return (
        Math.random().toString(36).substring(2, 15) +
        Math.random().toString(36).substring(2, 15)
      );
    }

    function checkAndSaveContact(invitationData, apiResponse) {
      $.ajax({
        url: "https://phpstack-1210427-4289046.cloudwaysapps.com/pages/check-contact-exists.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ contact: invitationData.contact }),
        success: function (checkResponse) {
          if (checkResponse.exists) {
            console.log("Contact already exists in the database.");
          } else {
            saveContactToDatabase(invitationData, apiResponse);
          }
        },
        error: function (xhr, status, error) {
          console.error("Check Contact Error:", error);
        },
      });
    }

    function saveContactToDatabase(invitationData, apiResponse) {
      // Handle all_users_detail and added_contacts to find the user ID
      let userId = null;
      if (
        apiResponse.all_users_detail &&
        apiResponse.all_users_detail.length > 0
      ) {
        userId = apiResponse.all_users_detail[0].id;
      } else if (
        apiResponse.added_contacts &&
        apiResponse.added_contacts.length > 0
      ) {
        userId = apiResponse.added_contacts[0].id;
      }

      // Prepare data to be sent to the PHP endpoint
      var saveData = {
        ...invitationData,
        user_token: apiResponse.user_token,
        user_id: userId,
      };

      // Send data to PHP endpoint to save in the database
      $.ajax({
        url: "https://phpstack-1210427-4289046.cloudwaysapps.com/pages/save-chatbot-details.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(saveData),
        success: function (saveResponse) {
          console.log("Save Response:", saveResponse);
        },
        error: function (xhr, status, error) {
          console.error("Save Error:", error);
        },
      });
    }

    // Function to handle normal message sending
    function sendNormalMessage(message) {
      // Handle normal message sending logic here
      // This is where you would typically send regular chat messages
      displayMessage(message, "user");
      // setTimeout(function () {
      //   var botResponse = getBotResponse(message);
      //   displayMessage(botResponse, "bot");
      // }, 500); // Simulate a delay for the bot response
    }

    // Function to display a message in the chat window
    function displayMessage(message, sender, imageUrl) {
      var messageElement;
      if (imageUrl) {
        messageElement = $(
          '<div class="message-item ' +
            sender +
            '">' +
            '<div class="msg-container" style="width: 100%;">' +
            '<div class="msg-content" style="justify-content: end;">' +
            '<div class="msg-content-text">' +
            '<img class="send-img" src="' +
            imageUrl +
            '" alt="Image">' +
            "<p>" +
            message +
            "</p>" +
            "</div>" +
            "</div>" +
            "</div>" +
            "</div>"
        );
      } else {
        messageElement = $(
          '<div class="message-item ' +
            sender +
            '">' +
            '<div class="msg-container" style="width: 100%;">' +
            '<div class="msg-content" style="justify-content: end;">' +
            '<div class="msg-content-text">' +
            "<p>" +
            message +
            "</p>" +
            "</div>" +
            "</div>" +
            "</div>" +
            "</div>"
        );
      }

      var __liMsg = $('<li class="message-item row"></li>').append(
        messageElement
      );
      $(".chatlist").append(__liMsg);
      // scroll latest li into view
      (__liMsg[0] || messageElement[0]).scrollIntoView({
        behavior: "smooth",
        block: "nearest",
      });
      return;

      // Use scrollIntoView for the latest message
      messageElement[0].scrollIntoView({
        behavior: "smooth",
        block: "nearest",
      });

      // Remove the "poclist" class
      $("#chat-list").removeClass("poclist");
    }

    // Function to get a response from the bot (placeholder logic)
    // function getBotResponse(userMessage) {
    //   // Placeholder bot response logic (you can replace this with actual bot integration)
    //   var responses = {
    //     hello: "Hello! How can I help you today?",
    //     hi: "Hi there! What can I do for you?",
    //     "how are you": "I'm just a bot, but I'm here to help!",
    //     bye: "Goodbye! Have a great day!",
    //   };
    //   return (
    //     responses[userMessage.toLowerCase()] ||
    //     "I'm not sure how to respond to that."
    //   );
    // }
  });
})(jQuery);

(function ($) {
  $(function () {
    var newsItems = window.roibotNewsItems || [];
    function renderNews(items) {
      var $ul = $("#news-list").empty();
      if (!items || !items.length) {
        $ul.append(
          '<li style="padding:10px 12px;opacity:.7">No news yet.</li>'
        );
        return;
      }
      items.forEach(function (it) {
        var t = it && it.title ? it.title : "Untitled";
        var u = it && it.url ? it.url : "#";
        $ul.append(
          '<li><a target="_blank" rel="noopener" href="' +
            u +
            '">' +
            t +
            "</a></li>"
        );
      });
    }
    $(".chatbot-footer .news")
      .off("click.roi")
      .on("click.roi", function () {
        $(".chatbot-footer li").removeClass("active");
        $(this).addClass("active");
        $("#news-list").removeClass("hidden");
        $("#chat-list, .message-form, .new-message-item").addClass("hidden");
        renderNews(newsItems);
      });
    $(".chatbot-footer .single-chat")
      .off("click.roi")
      .on("click.roi", function () {
        $(".chatbot-footer li").removeClass("active");
        $(this).addClass("active");
        $("#news-list").addClass("hidden");
        $("#chat-list, .message-form, .new-message-item").removeClass("hidden");
      });
  });
	
	
	
	// Tabs
	$(".chatbot-footer .news")
	  .off("click.roi")
	  .on("click.roi", function () {
		$(".chatbot-footer li").removeClass("active");
		$(this).addClass("active");
		$("#news-list").removeClass("hidden");
		$("#chat-list, .message-form, .new-message-item").addClass("hidden");
		renderNews(window.roibotNewsItems || []);
	  });

	$(".chatbot-footer .single-chat")
	  .off("click.roi")
	  .on("click.roi", function () {
		$(".chatbot-footer li").removeClass("active");
		$(this).addClass("active");
		$("#news-list").addClass("hidden");
		$("#chat-list, .message-form, .new-message-item").removeClass("hidden");
	  });
})(jQuery);

function escapeHtml(s) {
  return $("<div>")
    .text(s || "")
    .html();
}

function renderNews(items) {
  var $ul = $("#news-list").empty();
  if (!items || !items.length) {
    $ul.append('<li class="news-item empty">No news yet.</li>');
    return;
  }
  items.forEach(function (it) {
    var t = escapeHtml(it && it.title ? it.title : "Untitled");
    var u = it && it.url ? it.url : "#";
    var $li = $('<li class="news-item"></li>');
    $li.append('<span class="news-title">' + t + "</span>");
    $li.append(
      '<a class="news-link" target="_blank" rel="noopener" href="' +
        u +
        '">Open</a>'
    );
    $ul.append($li);
  });
}


