<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto relative shadow-md sm:rounded-lg p-6">
                    <div class="flex items-center mb-4">                    
                        <input id="default-checkbox" name="show" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-checkbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Show All Tasks</label>
                    </div>
                    <p id="message" class="hidden rounded-lg bg-sky-500/100 p-2 mb-4 text-center text-lg font-semibold text-blue-50 dark:text-blue-500/100">demo data</p>

                    <form id="data" method="POST">
                        <input type="hidden" id="user_id" value="{{auth()->user()->id}}">
                        <div class="flex">
                            <div class="relative w-full">
                                <input type="text" id="name" class="rounded-none rounded-r-lg bg-gray-50 border text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Project # To Do">

                                <button type="submit" class="absolute top-0 right-0 p-2.5 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    Add
                                    <span class="sr-only">Add</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-collapse border-slate-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>                                
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
 
    var deleteIcon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';

    function getData(status){
        $.ajax({ 
            url: "{{ route('tasks.index') }}",
            data:{status},
            success: function(data){
                var html = '';
                var deleted;
                for (var i=0; i<data.length; i++) {
                    if(data[i].deleted_at){
                        deleted = 'checked disabled';
                    }else{
                        deleted = '';
                    }

                    var tr = '<tr data-id="'+data[i].id+'" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">\
                    <td width="100"><input id="completeTask" type="checkbox" '+deleted+' value="'+data[i].id+'"></td>\
                    <td>'+data[i].name+'</td>\
                    <td><img class="max-w-xs rounded-full" src="'+data[i].user.img+'" alt="logo" </td>\
                    <td width="100"><button class="deleteTask" data-id="'+data[i].id+'">'+deleteIcon+'</td></tr>';
                    html +=tr;
                }
                $("#tbody").html(html);
            }
        });

    }
    //load tasks
    $(document).ready(function(){
        $("#default-checkbox").prop('checkbox',false);
        getData('active');
    });
    //create task
    $("form").submit(function (event) {
        event.preventDefault();
        var formData = {
          name: $("#name").val(),
          user_id:$("#user_id").val()
        };
        $.ajax({
          type: "POST",
          url: "{{ route('tasks.store') }}",
          data: formData,
            success: function (data) {
                console.log(data.message);
                $('#message').empty().show().html(data.message).delay(700).fadeOut('slow');
                getData('active');
                $("#name").val('');
            },
            error: function (data) {
                console.log(data.responseJSON.message);
                $('#message').empty().show().html(data.responseJSON.message).delay(700).fadeOut('slow');

            }
        });
    });
    //complete task
    $(document).on('change', '[type=checkbox]', function() {
        var tr = $(this).parents('tr');
        var task_id = $(this).val();
        if ($(this).is(':checked')==true && task_id>0) {
            $.ajax({
                type: "POST",
                url: "{{ route('tasks.completed') }}",
                data:{task_id},
                success: function (data) {
                    console.log(data.message);
                    $('#message').empty().show().html(data.message).delay(700).fadeOut('slow');
                    tr.remove();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

    });
    //delete task
    $('body').on('click', '.deleteTask', function (){
        var tr = $(this).parents('tr');
        var task_id = $(this).data("id");
        var result = confirm("Are you sure to delete this task");
        if(result){
            $.ajax({
                type: "DELETE",
                url: "tasks/"+task_id,
                success: function (data) {
                    console.log(data.message);
                    $('#message').empty().show().html(data.message).delay(700).fadeOut('slow');
                    tr.remove();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }else{
            return false;
        }
    });
    //show all taks
    $('#default-checkbox').on('click', function (){
        if(this.checked) {
            getData('all');
        }else{
            getData('active');
        }
        $(this).val(this.checked);
    });


</script>
</x-app-layout>
