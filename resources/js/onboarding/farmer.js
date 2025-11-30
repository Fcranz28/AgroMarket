// Farmer Onboarding Scripts
window.previewFile = function (input) {
   const previewBox = input.nextElementSibling;
   const uploadContent = input.previousElementSibling;
   const file = input.files[0];

   if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
         previewBox.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
         previewBox.style.display = 'block';
         uploadContent.style.display = 'none';
      }
      reader.readAsDataURL(file);
   }
}
