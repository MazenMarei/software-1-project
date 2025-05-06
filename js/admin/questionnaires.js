/**
 * Admin Questionnaires JavaScript
 * Handles questionnaire management functionality for admin
 */

// Global variables
let currentQuestionnaires = [];
let currentResponses = [];
let editingQuestionnaireId = null;
let nextQuestionId = 1;

// Document ready
document.addEventListener("DOMContentLoaded", function () {
  // Initialize the page
  initialize();

  // Event listeners
  document
    .getElementById("addQuestionBtn")
    .addEventListener("click", addNewQuestion);
  document
    .getElementById("saveQuestionnaireBtn")
    .addEventListener("click", saveQuestionnaire);
  document
    .getElementById("exportResponsesBtn")
    .addEventListener("click", exportResponsesToCSV);

  // Event delegation for dynamically created elements
  document
    .getElementById("questionnairesContainer")
    .addEventListener("click", handleQuestionnaireActions);
  document
    .getElementById("questionsList")
    .addEventListener("click", handleQuestionListActions);

  // Toggle sidebar on mobile
  const toggleSidebarBtn = document.querySelector(".toggle-sidebar");
  if (toggleSidebarBtn) {
    toggleSidebarBtn.addEventListener("click", function () {
      document.querySelector(".admin-sidebar").classList.toggle("show");
    });
  }

  // Initialize logout button
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      // Clear session/local storage
      localStorage.removeItem("authToken");
      sessionStorage.removeItem("currentUser");
      // Redirect to login page
      window.location.href = "../login.html";
    });
  }
});

/**
 * Initialize the page
 */
function initialize() {
  // Load questionnaires from API or local storage
  loadQuestionnaires();

  // Reset the form when the modal is opened for creating a new questionnaire
  const createQuestionnaireModal = document.getElementById(
    "createQuestionnaireModal"
  );
  if (createQuestionnaireModal) {
    createQuestionnaireModal.addEventListener(
      "show.bs.modal",
      function (event) {
        const button = event.relatedTarget;
        // If button doesn't have data-id, it's a new questionnaire
        if (!button || !button.hasAttribute("data-id")) {
          resetQuestionnaireForm();
          document.getElementById("modalTitle").textContent =
            "Create New Questionnaire";
          editingQuestionnaireId = null;
        }
      }
    );
  }
}

/**
 * Load questionnaires from API or local storage
 */
function loadQuestionnaires() {
  // For demo purposes, use sample data
  // In production, this would be an API call
  const sampleQuestionnaires = [
    {
      id: 1,
      name: "Artwork Preferences",
      description:
        "Helps match customers with artworks based on their style preferences and interests.",
      status: "active",
      questionCount: 12,
    },
    {
      id: 2,
      name: "Artist Application",
      description:
        "Application form for artists who want to sell their work on ArtShelf.",
      status: "active",
      questionCount: 8,
    },
    {
      id: 3,
      name: "Customer Satisfaction",
      description:
        "Survey to gather feedback on customer experience and satisfaction.",
      status: "draft",
      questionCount: 15,
    },
  ];

  currentQuestionnaires = sampleQuestionnaires;
  renderQuestionnaires();
}

/**
 * Render questionnaires to the page
 */
function renderQuestionnaires() {
  const container = document.getElementById("questionnairesContainer");
  if (!container) return;

  // Clear current content
  container.innerHTML = "";

  if (currentQuestionnaires.length === 0) {
    container.innerHTML =
      '<div class="col-12"><p class="text-center">No questionnaires found. Create your first questionnaire!</p></div>';
    return;
  }

  // Render each questionnaire
  currentQuestionnaires.forEach((questionnaire) => {
    const card = createQuestionnaireCard(questionnaire);
    container.appendChild(card);
  });
}

/**
 * Create a questionnaire card element
 * @param {Object} questionnaire - The questionnaire data
 * @returns {HTMLElement} - The questionnaire card element
 */
function createQuestionnaireCard(questionnaire) {
  const col = document.createElement("div");
  col.className = "col-md-6 col-lg-4 mb-4";

  col.innerHTML = `
        <div class="questionnaire-card">
            <div class="questionnaire-header">
                <h3 class="questionnaire-title">${questionnaire.name}</h3>
                <span class="questionnaire-status status-${
                  questionnaire.status
                }">${capitalizeFirstLetter(questionnaire.status)}</span>
            </div>
            <div class="question-count">${
              questionnaire.questionCount
            } questions</div>
            <p class="mt-2">${questionnaire.description}</p>
            <div class="action-buttons">
                <button class="btn btn-sm btn-outline-primary edit-questionnaire" data-id="${
                  questionnaire.id
                }">
                    <i class="fas fa-edit me-1"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-success view-responses" data-id="${
                  questionnaire.id
                }">
                    <i class="fas fa-eye me-1"></i> View Responses
                </button>
                <button class="btn btn-sm btn-outline-danger delete-questionnaire" data-id="${
                  questionnaire.id
                }">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    `;

  return col;
}

/**
 * Handle questionnaire card actions (edit, view responses, delete)
 * @param {Event} event - The click event
 */
function handleQuestionnaireActions(event) {
  const target = event.target.closest("button");
  if (!target) return;

  const questionnaireId = parseInt(target.getAttribute("data-id"));

  if (target.classList.contains("edit-questionnaire")) {
    openEditQuestionnaireModal(questionnaireId);
  } else if (target.classList.contains("view-responses")) {
    openViewResponsesModal(questionnaireId);
  } else if (target.classList.contains("delete-questionnaire")) {
    if (
      confirm(
        "Are you sure you want to delete this questionnaire? This action cannot be undone."
      )
    ) {
      deleteQuestionnaire(questionnaireId);
    }
  }
}

/**
 * Open the edit questionnaire modal and populate it with data
 * @param {number} questionnaireId - The ID of the questionnaire to edit
 */
function openEditQuestionnaireModal(questionnaireId) {
  // Find the questionnaire in the current data
  const questionnaire = currentQuestionnaires.find(
    (q) => q.id === questionnaireId
  );
  if (!questionnaire) return;

  // Set editing ID
  editingQuestionnaireId = questionnaireId;

  // Update modal title
  document.getElementById("modalTitle").textContent = "Edit Questionnaire";

  // Populate form fields
  document.getElementById("questionnaireId").value = questionnaire.id;
  document.getElementById("questionnaireName").value = questionnaire.name;
  document.getElementById("questionnaireDescription").value =
    questionnaire.description;

  // Set status radio button
  const statusRadio = document.querySelector(
    `input[name="questionnaireStatus"][value="${questionnaire.status}"]`
  );
  if (statusRadio) statusRadio.checked = true;

  // Load questions (in a real app, this would fetch from API)
  loadQuestionnaireQuestions(questionnaireId);

  // Show the modal
  const modal = new bootstrap.Modal(
    document.getElementById("createQuestionnaireModal")
  );
  modal.show();
}

/**
 * Load questions for a questionnaire
 * @param {number} questionnaireId - The ID of the questionnaire
 */
function loadQuestionnaireQuestions(questionnaireId) {
  // In a real app, this would fetch from an API
  // For demo purposes, we'll use some sample questions
  const sampleQuestions = [
    {
      id: 1,
      type: "radio",
      text: "What type of art are you interested in?",
      options: ["Paintings", "Sculptures", "Photography", "Digital Art"],
    },
    {
      id: 2,
      type: "checkbox",
      text: "Which art styles do you prefer?",
      options: [
        "Abstract",
        "Modern",
        "Contemporary",
        "Classical",
        "Impressionist",
      ],
    },
    {
      id: 3,
      type: "text",
      text: "Describe your ideal artwork in a few words:",
      options: [],
    },
  ];

  // Clear questions list
  const questionsList = document.getElementById("questionsList");
  questionsList.innerHTML = "";

  // Render each question
  sampleQuestions.forEach((question) => {
    const questionElement = createQuestionElement(question);
    questionsList.appendChild(questionElement);
  });

  // Update next question ID
  nextQuestionId = sampleQuestions.length + 1;
}

/**
 * Create a question element for the form
 * @param {Object} question - The question data
 * @returns {HTMLElement} - The question element
 */
function createQuestionElement(question) {
  const questionItem = document.createElement("div");
  questionItem.className = "question-item";
  questionItem.setAttribute("data-question-id", question.id);

  let optionsHTML = "";
  if (
    ["radio", "checkbox", "select"].includes(question.type) &&
    question.options.length > 0
  ) {
    optionsHTML = `
            <div class="question-options mt-2">
                ${question.options
                  .map(
                    (option) => `
                    <div class="option-item">
                        <input type="text" class="form-control form-control-sm option-text" placeholder="Option" value="${option}">
                        <button type="button" class="btn btn-sm btn-link text-danger remove-option">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `
                  )
                  .join("")}
                <button type="button" class="btn btn-sm btn-link add-option">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>
        `;
  }

  questionItem.innerHTML = `
        <div class="question-item-header">
            <div>
                <i class="fas fa-grip-vertical question-drag-handle"></i>
                <select class="form-select-sm question-type">
                    <option value="text" ${
                      question.type === "text" ? "selected" : ""
                    }>Text</option>
                    <option value="radio" ${
                      question.type === "radio" ? "selected" : ""
                    }>Multiple Choice</option>
                    <option value="checkbox" ${
                      question.type === "checkbox" ? "selected" : ""
                    }>Checkbox</option>
                    <option value="select" ${
                      question.type === "select" ? "selected" : ""
                    }>Dropdown</option>
                </select>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mt-2">
            <input type="text" class="form-control question-text" placeholder="Question text" value="${
              question.text
            }">
            ${optionsHTML}
        </div>
    `;

  // Add event listener for question type change
  const typeSelect = questionItem.querySelector(".question-type");
  typeSelect.addEventListener("change", function () {
    handleQuestionTypeChange(questionItem, this.value);
  });

  return questionItem;
}

/**
 * Handle question type change
 * @param {HTMLElement} questionItem - The question item element
 * @param {string} newType - The new question type
 */
function handleQuestionTypeChange(questionItem, newType) {
  const hasOptionsContainer = questionItem.querySelector(".question-options");

  // If changing to a type that needs options
  if (["radio", "checkbox", "select"].includes(newType)) {
    // If options container doesn't exist, create it
    if (!hasOptionsContainer) {
      const optionsContainer = document.createElement("div");
      optionsContainer.className = "question-options mt-2";
      optionsContainer.innerHTML = `
                <div class="option-item">
                    <input type="text" class="form-control form-control-sm option-text" placeholder="Option" value="Option 1">
                    <button type="button" class="btn btn-sm btn-link text-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-link add-option">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            `;
      questionItem.querySelector(".mt-2").appendChild(optionsContainer);
    }
  } else {
    // If changing to a type that doesn't need options, remove them
    if (hasOptionsContainer) {
      hasOptionsContainer.remove();
    }
  }
}

/**
 * Handle question list actions (add/remove options, remove questions)
 * @param {Event} event - The click event
 */
function handleQuestionListActions(event) {
  const target = event.target.closest("button");
  if (!target) return;

  if (target.classList.contains("add-option")) {
    addNewOption(target);
  } else if (target.classList.contains("remove-option")) {
    removeOption(target);
  } else if (target.classList.contains("remove-question")) {
    removeQuestion(target);
  }
}

/**
 * Add a new option to a question
 * @param {HTMLElement} button - The "Add Option" button
 */
function addNewOption(button) {
  const optionsContainer = button.closest(".question-options");
  const newOption = document.createElement("div");
  newOption.className = "option-item";
  newOption.innerHTML = `
        <input type="text" class="form-control form-control-sm option-text" placeholder="Option" value="">
        <button type="button" class="btn btn-sm btn-link text-danger remove-option">
            <i class="fas fa-times"></i>
        </button>
    `;
  optionsContainer.insertBefore(newOption, button);
}

/**
 * Remove an option from a question
 * @param {HTMLElement} button - The "Remove Option" button
 */
function removeOption(button) {
  const optionItem = button.closest(".option-item");
  const optionsContainer = optionItem.closest(".question-options");

  // Don't remove if it's the last option
  const optionItems = optionsContainer.querySelectorAll(".option-item");
  if (optionItems.length <= 1) {
    alert("At least one option is required for this question type.");
    return;
  }

  optionItem.remove();
}

/**
 * Remove a question from the form
 * @param {HTMLElement} button - The "Remove Question" button
 */
function removeQuestion(button) {
  const questionItem = button.closest(".question-item");
  questionItem.remove();
}

/**
 * Add a new question to the form
 */
function addNewQuestion() {
  const questionsList = document.getElementById("questionsList");
  const newQuestion = {
    id: nextQuestionId++,
    type: "text",
    text: "",
    options: [],
  };

  const questionElement = createQuestionElement(newQuestion);
  questionsList.appendChild(questionElement);
}

/**
 * Save the questionnaire form
 */
function saveQuestionnaire() {
  // Get form values
  const formData = {
    id: editingQuestionnaireId || Date.now(),
    name: document.getElementById("questionnaireName").value,
    description: document.getElementById("questionnaireDescription").value,
    status: document.querySelector('input[name="questionnaireStatus"]:checked')
      .value,
    questions: getQuestionsFromForm(),
  };

  // Validate form
  if (!formData.name) {
    alert("Please enter a name for the questionnaire.");
    return;
  }

  if (formData.questions.length === 0) {
    alert("Please add at least one question to the questionnaire.");
    return;
  }

  // In a real app, this would send data to an API
  // For demo purposes, update the local data

  // Find if questionnaire already exists
  const existingIndex = currentQuestionnaires.findIndex(
    (q) => q.id === formData.id
  );

  if (existingIndex !== -1) {
    // Update existing questionnaire
    currentQuestionnaires[existingIndex] = {
      ...formData,
      questionCount: formData.questions.length,
    };
  } else {
    // Add new questionnaire
    currentQuestionnaires.push({
      ...formData,
      questionCount: formData.questions.length,
    });
  }

  // Hide modal
  const modalElement = document.getElementById("createQuestionnaireModal");
  const modal = bootstrap.Modal.getInstance(modalElement);
  modal.hide();

  // Update the display
  renderQuestionnaires();

  // Show success message
  alert(
    editingQuestionnaireId
      ? "Questionnaire updated successfully!"
      : "Questionnaire created successfully!"
  );

  // Reset the editing ID
  editingQuestionnaireId = null;
}

/**
 * Get questions data from the form
 * @returns {Array} - Array of question objects
 */
function getQuestionsFromForm() {
  const questions = [];
  const questionItems = document.querySelectorAll(
    "#questionsList .question-item"
  );

  questionItems.forEach((item) => {
    const id = parseInt(item.getAttribute("data-question-id"));
    const type = item.querySelector(".question-type").value;
    const text = item.querySelector(".question-text").value;

    let options = [];
    if (["radio", "checkbox", "select"].includes(type)) {
      const optionInputs = item.querySelectorAll(".option-text");
      optionInputs.forEach((input) => {
        if (input.value.trim()) {
          options.push(input.value.trim());
        }
      });
    }

    questions.push({ id, type, text, options });
  });

  return questions;
}

/**
 * Reset the questionnaire form
 */
function resetQuestionnaireForm() {
  // Clear the form fields
  document.getElementById("questionnaireId").value = "";
  document.getElementById("questionnaireName").value = "";
  document.getElementById("questionnaireDescription").value = "";

  // Reset status to "draft"
  document.getElementById("statusDraft").checked = true;

  // Clear questions list except for one empty question
  const questionsList = document.getElementById("questionsList");
  questionsList.innerHTML = "";

  // Add one empty question
  const emptyQuestion = {
    id: 1,
    type: "text",
    text: "",
    options: [],
  };

  const questionElement = createQuestionElement(emptyQuestion);
  questionsList.appendChild(questionElement);

  // Reset next question ID
  nextQuestionId = 2;
}

/**
 * Delete a questionnaire
 * @param {number} questionnaireId - The ID of the questionnaire to delete
 */
function deleteQuestionnaire(questionnaireId) {
  // In a real app, this would send a request to an API
  // For demo purposes, remove from local data
  const index = currentQuestionnaires.findIndex(
    (q) => q.id === questionnaireId
  );

  if (index !== -1) {
    currentQuestionnaires.splice(index, 1);
    renderQuestionnaires();
    alert("Questionnaire deleted successfully!");
  }
}

/**
 * Open the view responses modal
 * @param {number} questionnaireId - The ID of the questionnaire
 */
function openViewResponsesModal(questionnaireId) {
  // Find the questionnaire in the current data
  const questionnaire = currentQuestionnaires.find(
    (q) => q.id === questionnaireId
  );
  if (!questionnaire) return;

  // Update modal title
  document.getElementById(
    "responsesTitle"
  ).textContent = `Responses: ${questionnaire.name}`;

  // Load responses (in a real app, this would fetch from API)
  loadQuestionnaireResponses(questionnaireId);

  // Show the modal
  const modal = new bootstrap.Modal(
    document.getElementById("viewResponsesModal")
  );
  modal.show();
}

/**
 * Load responses for a questionnaire
 * @param {number} questionnaireId - The ID of the questionnaire
 */
function loadQuestionnaireResponses(questionnaireId) {
  // In a real app, this would fetch from an API
  // For demo purposes, we'll use some sample data
  const sampleResponses = [
    {
      id: 1001,
      questionnaireId: 1,
      respondent: "John Smith",
      email: "john.smith@example.com",
      date: "April 15, 2025",
      status: "complete",
      answers: [
        { questionId: 1, value: "Paintings" },
        { questionId: 2, value: ["Modern", "Contemporary"] },
        {
          questionId: 3,
          value: "Colorful abstract pieces that make a statement",
        },
      ],
    },
    {
      id: 1002,
      questionnaireId: 1,
      respondent: "Sarah Johnson",
      email: "sarah.j@example.com",
      date: "April 17, 2025",
      status: "complete",
      answers: [
        { questionId: 1, value: "Photography" },
        { questionId: 2, value: ["Modern"] },
        {
          questionId: 3,
          value: "Black and white photography with urban themes",
        },
      ],
    },
    {
      id: 1003,
      questionnaireId: 1,
      respondent: "Michael Brown",
      email: "mbrown@example.com",
      date: "April 20, 2025",
      status: "partial",
      answers: [
        { questionId: 1, value: "Sculptures" },
        { questionId: 2, value: ["Classical"] },
      ],
    },
    {
      id: 2001,
      questionnaireId: 2,
      respondent: "Emma Wilson",
      email: "emma.w@example.com",
      date: "April 10, 2025",
      status: "complete",
      answers: [],
    },
  ];

  // Filter responses for this questionnaire
  currentResponses = sampleResponses.filter(
    (response) => response.questionnaireId === questionnaireId
  );

  // Render to table
  renderResponses();
}

/**
 * Render responses to the table
 */
function renderResponses() {
  const tableBody = document.querySelector("#responsesTable tbody");
  if (!tableBody) return;

  // Clear current content
  tableBody.innerHTML = "";

  if (currentResponses.length === 0) {
    tableBody.innerHTML =
      '<tr><td colspan="5" class="text-center">No responses found for this questionnaire.</td></tr>';
    return;
  }

  // Render each response
  currentResponses.forEach((response) => {
    const row = document.createElement("tr");

    row.innerHTML = `
            <td>${response.id}</td>
            <td>${response.respondent}</td>
            <td>${response.date}</td>
            <td><span class="badge bg-${
              response.status === "complete" ? "success" : "warning"
            }">${capitalizeFirstLetter(response.status)}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary view-detail" data-id="${
                  response.id
                }">
                    View Details
                </button>
            </td>
        `;

    // Add event listener to view details button
    const viewButton = row.querySelector(".view-detail");
    viewButton.addEventListener("click", () => showResponseDetails(response));

    tableBody.appendChild(row);
  });
}

/**
 * Show response details
 * @param {Object} response - The response data
 */
function showResponseDetails(response) {
  // In a real app, this would show a detailed view of the response
  // For demo purposes, show an alert with some details
  const details = `
        Response ID: ${response.id}
        Respondent: ${response.respondent}
        Email: ${response.email}
        Date: ${response.date}
        Status: ${capitalizeFirstLetter(response.status)}
        
        Number of Questions Answered: ${response.answers.length}
    `;

  alert(details);
}

/**
 * Export responses to CSV
 */
function exportResponsesToCSV() {
  // In a real app, this would generate a CSV file with all responses
  // For demo purposes, show a message
  alert(
    "CSV export functionality would be implemented here. In a real app, this would download a CSV file with all response data."
  );
}

/**
 * Utility function to capitalize the first letter of a string
 * @param {string} string - The string to capitalize
 * @returns {string} - The capitalized string
 */
function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}
