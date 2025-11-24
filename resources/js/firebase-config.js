// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getAuth } from "firebase/auth";

// Your web app's Firebase configuration
const firebaseConfig = {
   apiKey: "AIzaSyCbeWFHa1ubvm3zJO0b8C4zJ3vZyc8C_Go",
   authDomain: "agromarket-2b294.firebaseapp.com",
   projectId: "agromarket-2b294",
   storageBucket: "agromarket-2b294.firebasestorage.app",
   messagingSenderId: "72825968964",
   appId: "1:72825968964:web:bab517dfd2542b0dbd5f11",
   measurementId: "G-L6PBJ0ZBLG"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);

export { app, analytics, auth };
