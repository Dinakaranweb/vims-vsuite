<style>
    #staff-suggestions {
        width: 300px; 
        max-height: 200px; 
        overflow-y: auto; 
        position: absolute; 
        z-index: 1000; 
        left: 0; 
    }
    #dept-suggestions{
        width: 220px; 
        max-height: 200px; 
        overflow-y: auto; 
        position: absolute; 
        z-index: 1000; 
        left: 0;
    }
    .dropdown-item label {
        display: block;
        padding: 0.25rem 1rem;
        margin: 0;
        cursor: pointer;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    .dropdown-header {
        padding: 0.5rem 1rem;
        font-weight: bold;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    .badge-sm {
        font-size: 0.7em;
        padding: 0.2em 0.4em;
    }
    .badge-info {
        background-color: #17a2b8;
    }
    mark {
        background-color: #ffeb3b;
        padding: 0;
        border-radius: 2px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Initialize selectedDepartments - handle both cases where $forwardedTo may not exist
    let selectedDepartments = [];
    
    // Check if forwardedTo exists (for edit pages) or initialize empty array
    @if(isset($forwardedTo) && !empty($forwardedTo))
        selectedDepartments = {!! json_encode($forwardedTo) !!};
    @else
        // Try to get from existing input value (for cases where page reloads)
        $(document).ready(function() {
            const existingValue = $('#forward_to_search').val();
            if (existingValue && existingValue.trim() !== '') {
                selectedDepartments = existingValue.split(',').map(item => item.trim());
                renderSelectedDepartments();
            }
        });
    @endif

    // Function to render selected departments as tags
    function renderSelectedDepartments() {
        const tagsContainer = $('#selected-departments');
        tagsContainer.empty(); // Clear previous tags

        selectedDepartments.forEach(dept => {
            tagsContainer.append(`<span class="badge badge-danger mr-1 mb-1">${dept} <span class="remove-tag" data-dept="${dept}" style="cursor: pointer; margin-left: 5px;">&times;</span></span>`);
        });

        // Update the hidden input to hold selected departments
        $('#forward_to_search').val(selectedDepartments.join(', '));
    }

    // Helper function to highlight matching text
    function highlightText(text, query) {
        if (!query) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    $(document).ready(function () {
        // Initial render of selected departments (if any exist)
        renderSelectedDepartments();

        $('#post_to').on('input', function () {
            var query = $(this).val();

            if (query.length > 1) {
                $.ajax({
                    url: "{{ route('search.departments') }}",
                    method: "GET",
                    data: { query: query },
                    success: function (data) {
                        var suggestions = '';
                        if (data.length > 0) {
                            data.forEach(function (dept) {
                                // Handle both old format (string) and new format (object)
                                const deptName = typeof dept === 'string' ? dept : dept.dept_label;
                                suggestions += '<a href="#" class="dropdown-item dept-item">' + deptName + '</a>';
                            });
                        } else {
                            suggestions += '<a href="#" class="dropdown-item disabled">No departments found</a>';
                        }
                        
                        $('#dept-suggestions').html(suggestions).show();
                    }
                });
            } else {
                $('#dept-suggestions').hide();
            }
        });
        
        // Handle click on department suggestion
        $(document).on('click', '.dept-item', function (e) {
            e.preventDefault();
            var selectedDept = $(this).text();
            $('#post_to').val(selectedDept);
            $('#dept-suggestions').hide();
        });

        // Hide suggestions if clicked outside
        $(document).click(function (e) {
            if (!$(e.target).closest('#dept-suggestions, #post_to').length) {
                $('#dept-suggestions').hide();
            }
        });

        // Fetch and display suggestions as a dropdown list with checkboxes
        $('#forward_to_search').on('input', function () {
            const query = $(this).val();

            if (query.length > 1) {
                $.ajax({
                    url: "{{ route('search.departments') }}",
                    method: "GET",
                    data: { query: query },
                    success: function (data) {
                        
                        let suggestions = '';
                        const queryLower = query.toLowerCase();
                        
                        if (data && data.length > 0) {
                            // Add a header to show what we're searching
                            suggestions += `<div class="dropdown-header">Search results for "${query}"</div>`;
                            
                            data.forEach(item => {
                                const deptLabel = typeof item === 'string' ? item : item.dept_label;
                                const headName = typeof item === 'string' ? '' : item.head_name;
                                const type = typeof item === 'string' ? 'department' : item.type;
                                
                                const isChecked = selectedDepartments.includes(deptLabel) ? 'checked' : '';
                                
                                // Highlight matching text
                                const highlightedDept = highlightText(deptLabel, query);
                                const highlightedHead = headName ? highlightText(headName, query) : '';
                                
                                suggestions += `
                                    <div class="dropdown-item">
                                        <label>
                                            <input type="checkbox" class="dept-checkbox" 
                                                   value="${deptLabel}" 
                                                   ${isChecked}>
                                            <strong>${highlightedDept}</strong>`;
                                
                                // Only show head name if available
                                if (headName) {
                                    suggestions += `<small class="text-muted ml-2">- ${highlightedHead}</small>`;
                                }
                                
                                // Show search context badge
                                //suggestions += `<span class="badge badge-info badge-sm ml-2">${type}</span>`;
                                
                                suggestions += `</label></div>`;
                            });
                        } else {
                            suggestions = '<div class="dropdown-item disabled">No departments or heads found</div>';
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

        $('#staff_name').on('input', function () {
            var query = $(this).val();

            if (query.length > 1) {
                $.ajax({
                    url: "{{ route('search.staff') }}",
                    method: "GET",
                    data: { query: query },
                    success: function (data) {
                        var suggestions = '';

                        if (data.length > 0) {
                            data.forEach(function (staff) {
                                suggestions += '<a href="#" class="dropdown-item staff-item" data-id="' + staff.id + '">' 
                                                + staff.name + ' - ' + staff.department + '</a>';
                            });
                        } else {
                            suggestions += '<a href="#" class="dropdown-item disabled">No staff found</a>';
                        }

                        $('#staff-suggestions').html(suggestions).show();
                    }
                });
            } else {
                $('#staff-suggestions').hide();
            }
        });

        // Handle click on staff suggestion
        $(document).on('click', '.staff-item', function (e) {
            e.preventDefault();
            var selectedStaff = $(this).text();
            var selectedId = $(this).data('id');
            
            $('#staff_name').val(selectedStaff);
            $('#staff_id').val(selectedId);
            $('#staff-suggestions').hide();
        });

        // If no matching name is found, set staff_id to null
        $('#staff_name').on('blur', function () {
            if ($('#staff_suggestions .dropdown-item').length === 0 || $('#staff_suggestions .dropdown-item.disabled').length) {
                $('#staff_id').val(null);
            }
        });

        // Hide suggestions if clicked outside
        $(document).click(function (e) {
            if (!$(e.target).closest('#staff-suggestions, #staff_name').length) {
                $('#staff-suggestions').hide();
            }
        });
    });
</script>