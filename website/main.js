function showForm(formId){
    // Hide all forms
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    
    // Show the selected form
    document.getElementById(formId).classList.add("active");
    
    // Update active tab
    document.querySelectorAll(".form-tab").forEach(tab => tab.classList.remove("active"));
    
    // Activate the corresponding tab
    if(formId === 'login-form') {
        document.querySelector(".form-tab:first-child").classList.add("active");
    } else {
        document.querySelector(".form-tab:last-child").classList.add("active");
    }
}

// Add event listeners when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Make sure the login form is active by default
    showForm('login-form');
    
    // Add click event listeners to tabs
    document.querySelectorAll('.form-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const formId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            showForm(formId);
        });
    });
    
    // Prevent default behavior for auth links
    document.querySelectorAll('.auth-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const formId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            showForm(formId);
        });
    });
});