// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    });
  }
  
  // Dropdown hover logic
  const userDropdown = document.querySelector('.user-dropdown');
  const dropdownContent = document.querySelector('.dropdown-content');
  
  userDropdown.addEventListener('mouseenter', () => {
    dropdownContent.style.display = 'block';
  });
  
  userDropdown.addEventListener('mouseleave', () => {
    dropdownContent.style.display = 'none';
  });
  
  // Add smooth transition effect
  dropdownContent.style.transition = 'opacity 0.3s ease';
  