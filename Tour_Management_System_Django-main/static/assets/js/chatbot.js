const sendButton = document.querySelector("#send-button");
const userInput = document.querySelector("#user-input");
const chatHistory = document.querySelector(".chat-history");

sendButton.addEventListener("click", () => {
    // Get the user input text
    const inputText = userInput.value;

    // Create a new chat message element
    const chatMessage = document.createElement("div");
    chatMessage.classList.add("chat-message");

    // Add the user input text to the chat message element
    const userText = document.createElement("p");
    userText.textContent = inputText;
    chatMessage.appendChild(userText);

    // Add the chat message element to the chat history container
    chatHistory.appendChild(chatMessage);

    // Send the user input text to the chatbot backend
    // and receive the chatbot response text
    // You'll need to implement this functionality in your chatbot backend code

    // Create a new chat message element for the chatbot response
    const chatbotMessage = document.createElement("div");
    chatbotMessage.classList.add("chat-message");

    // Add the chatbot response text to the chat message element
    const chatbotText = document.createElement("p");
    chatbotText.textContent = "I am Fine , Thanks for asking ";
    chatbotMessage.appendChild(chatbotText);

    // Add the chat message element to the chat history container
    chatHistory.appendChild(chatbotMessage);

    // Clear the user input text
    userInput.value = "";
});
