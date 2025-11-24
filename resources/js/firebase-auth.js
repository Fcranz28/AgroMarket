import { auth } from './firebase-config';
import {
   signInWithPopup,
   GoogleAuthProvider,
   FacebookAuthProvider
} from 'firebase/auth';

// Google Sign In
export async function signInWithGoogle(isRegistering = false) {
   const provider = new GoogleAuthProvider();
   try {
      const result = await signInWithPopup(auth, provider);
      const user = result.user;
      const idToken = await user.getIdToken();

      // Send token to backend
      await authenticateWithBackend(idToken, 'google', isRegistering);

      return { success: true, user };
   } catch (error) {
      console.error('Google sign-in error:', error);
      return { success: false, error: error.message };
   }
}

// Facebook Sign In
export async function signInWithFacebook(isRegistering = false) {
   const provider = new FacebookAuthProvider();
   try {
      const result = await signInWithPopup(auth, provider);
      const user = result.user;
      const idToken = await user.getIdToken();

      // Send token to backend
      await authenticateWithBackend(idToken, 'facebook', isRegistering);

      return { success: true, user };
   } catch (error) {
      console.error('Facebook sign-in error:', error);
      return { success: false, error: error.message };
   }
}

// Send Firebase token to Laravel backend
async function authenticateWithBackend(idToken, provider, isRegistering = false) {
   const response = await fetch('/auth/firebase', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({
         idToken,
         provider,
         isRegistering
      })
   });

   const data = await response.json();

   if (!response.ok) {
      throw new Error(data.error || 'Error de autenticación');
   }

   // Handle different actions - redirect directly without alerts
   if (data.action === 'redirect_to_register') {
      // User doesn't exist, redirect to register
      window.location.href = data.redirect;
      return data;
   }

   if (data.action === 'redirect_to_login') {
      // User already exists, redirect to login
      window.location.href = data.redirect;
      return data;
   }

   // Successful login or registration
   if (data.redirect) {
      window.location.href = data.redirect;
   }

   return data;
}

// Initialize social login buttons
export function initializeSocialLogin() {
   // Determine if we're on register or login page
   const isRegisterPage = window.location.pathname.includes('/register');

   // Google button
   const googleBtns = document.querySelectorAll('.google-login-btn');
   googleBtns.forEach(btn => {
      btn.addEventListener('click', async (e) => {
         e.preventDefault();
         btn.disabled = true;

         const originalHTML = btn.innerHTML;
         btn.innerHTML = '<span>Conectando...</span>';

         const result = await signInWithGoogle(isRegisterPage);

         if (!result.success) {
            alert('Error al iniciar sesión con Google: ' + result.error);
            btn.disabled = false;
            btn.innerHTML = originalHTML;
         }
      });
   });

   // Facebook button
   const facebookBtns = document.querySelectorAll('.facebook-login-btn');
   facebookBtns.forEach(btn => {
      btn.addEventListener('click', async (e) => {
         e.preventDefault();
         btn.disabled = true;

         const originalHTML = btn.innerHTML;
         btn.innerHTML = '<span>Conectando...</span>';

         const result = await signInWithFacebook(isRegisterPage);

         if (!result.success) {
            alert('Error al iniciar sesión con Facebook: ' + result.error);
            btn.disabled = false;
            btn.innerHTML = originalHTML;
         }
      });
   });
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', initializeSocialLogin);
