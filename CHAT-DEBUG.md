# Debug Chat System

To help debug the chat system, here are some JavaScript commands you can run in the browser console:

## Clear Chat Storage
```javascript
// Clear any stored conversation ID
localStorage.removeItem('activeConversationId');
window.conversationId = null;
console.log('Chat storage cleared');
```

## Check Current State
```javascript
// Check what's stored
console.log('Stored conversation ID:', localStorage.getItem('activeConversationId'));
console.log('Window conversation ID:', window.conversationId);
```

## Test Chat Start
```javascript
// Test the chat start endpoint directly
fetch('/chat/start', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        escalation_context: null,
        initial_message: 'Hello, I need assistance.'
    })
})
.then(response => response.json())
.then(data => {
    console.log('Chat start response:', data);
})
.catch(error => {
    console.error('Error:', error);
});
```

## Force Start Queue Chat
```javascript
// Force start a new queue chat session
window.conversationId = null;
localStorage.removeItem('activeConversationId');
startQueueChat();
```

## Common Issues and Solutions:

1. **"Resuming Chat" loop**: Stored conversation ID from terminated chat
   - **Solution**: Clear localStorage with the first command above

2. **Not entering queue**: Server thinks conversation is active
   - **Solution**: Check database for conversation status and update if needed

3. **JavaScript errors**: Missing DOM elements
   - **Solution**: Ensure chat widget is loaded before calling functions