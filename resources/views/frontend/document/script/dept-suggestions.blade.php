<style>
    
    #forward-suggestions {
        z-index: 1000;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .dropdown-item {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item.disabled {
        color: #6c757d;
        pointer-events: none;
    }
    
    .badge-danger {
        background-color: #dc3545;
        padding: 0.5em 0.75em;
        font-size: 0.875rem;
    }
    
    .remove-tag {
        font-size: 1.2em;
        line-height: 0.8;
    }
    
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    
    // Initialize selectedDepartments from existing forwarded_to data
    let selectedDepartments = {!! json_encode($forwardedTo) !!};
    
    // Function to render selected departments as tags
    function renderSelectedDepartments() {
        let tagsHtml = '';
        selectedDepartments.forEach(dept => {
            tagsHtml += `
                <span class="badge badge-danger mr-1 mb-1">
                    ${dept}
                    <span class="remove-tag" data-dept="${dept}" style="cursor: pointer; margin-left: 5px;">&times;</span>
                </span>`;
        });
        $('#selected-departments').html(tagsHtml);
        
        // Also update the hidden input value for form submission
        $('input[name="forward_to"]').val(selectedDepartments.join(', '));
    }
    
    // Initial render
    renderSelectedDepartments();
    
    $('#forward_to_search').on('input', function () {
        const query = $(this).val();
    
        if (query.length > 1) {
            $.ajax({
                url: "{{ route('search.departments') }}",
                method: "GET",
                data: { query: query },
                success: function (data) {
                    
                    let suggestions = '';
                    
                    if (data && data.length > 0) {
                        data.forEach(dept => {
                            const isChecked = selectedDepartments.includes(dept.dept_label) ? 'checked' : '';
                            suggestions += `
                                <div class="dropdown-item">
                                    <label style="display: block; padding: 0.25rem 1rem; margin: 0; cursor: pointer;">
                                        <input type="checkbox" class="dept-checkbox" 
                                               value="${dept.dept_label}" 
                                               data-head-name="${dept.head_name}" 
                                               ${isChecked}>
                                        <strong>${dept.dept_label}</strong>
                                        <small class="text-muted ml-2">- ${dept.head_name}</small>
                                    </label>
                                </div>`;
                        });
                    } else {
                        suggestions = '<div class="dropdown-item disabled">No departments found</div>';
                    }
    
                    $('#forward-suggestions').html(suggestions).show();
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        } else {
            $('#forward-suggestions').hide();
        }
    });
    
    // Handle selection and deselection of departments
    $(document).on('change', '.dept-checkbox', function () {
        const dept = $(this).val();
    
        if (this.checked) {
            if (!selectedDepartments.includes(dept)) {
                selectedDepartments.push(dept);
            }
        } else {
            selectedDepartments = selectedDepartments.filter(item => item !== dept);
        }
    
        renderSelectedDepartments();
    });
    
    // Handle tag removal
    $(document).on('click', '.remove-tag', function () {
        const dept = $(this).data('dept');
        selectedDepartments = selectedDepartments.filter(item => item !== dept);
    
        // Uncheck the corresponding checkbox if visible in suggestions
        $(`#forward-suggestions input[value="${dept}"]`).prop('checked', false);
    
        renderSelectedDepartments();
    });
    
    // Hide suggestions when clicking outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#forward-suggestions, #forward_to_search').length) {
            $('#forward-suggestions').hide();
        }
    });
    
    // Show suggestions when focusing on input
    $('#forward_to_search').on('focus', function () {
        if ($('#forward-suggestions').html().trim() !== '') {
            $('#forward-suggestions').show();
        }
    });
    
</script>