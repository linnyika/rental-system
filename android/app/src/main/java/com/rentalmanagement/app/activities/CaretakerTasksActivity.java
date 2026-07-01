package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.TaskAdapter;
import com.rentalmanagement.app.databinding.ActivityCaretakerTasksBinding;
import com.rentalmanagement.app.models.TaskItem;
import com.rentalmanagement.app.models.TaskActionResponse;
import com.rentalmanagement.app.models.TasksResponse;
import com.rentalmanagement.app.repository.MaintenanceRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CaretakerTasksActivity extends BaseDashboardActivity {

    private ActivityCaretakerTasksBinding binding;
    private MaintenanceRepository maintenanceRepository;
    private final List<TaskItem> tasks = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityCaretakerTasksBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        maintenanceRepository = new MaintenanceRepository();

        configureToolbar(binding.toolbar, "Assigned Tasks", true);

        binding.rvTasks.setLayoutManager(new LinearLayoutManager(this));
        binding.rvTasks.setNestedScrollingEnabled(false);

        binding.swipeRefreshLayout.setOnRefreshListener(this::loadTasks);
        binding.btnRetry.setOnClickListener(v -> loadTasks());

        loadTasks();
    }

    private void loadTasks() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.caretakerTasks(authorization).enqueue(new Callback<TasksResponse>() {
            @Override
            public void onResponse(Call<TasksResponse> call, Response<TasksResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load tasks.");
                    showError(parsedError.getMessage());
                    return;
                }

                tasks.clear();
                if (response.body().getTasks() != null) {
                    tasks.addAll(response.body().getTasks());
                }
                renderTasks();
            }

            @Override
            public void onFailure(Call<TasksResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load tasks. Please try again.");
            }
        });
    }

    private void renderTasks() {
        if (tasks.isEmpty()) {
            binding.tvEmpty.setVisibility(View.VISIBLE);
            binding.rvTasks.setVisibility(View.GONE);
            binding.rvTasks.setAdapter(new TaskAdapter(new ArrayList<>(), TaskAdapter.Mode.CARETAKER, null));
            return;
        }

        binding.tvEmpty.setVisibility(View.GONE);
        binding.rvTasks.setVisibility(View.VISIBLE);
        binding.rvTasks.setAdapter(new TaskAdapter(tasks, TaskAdapter.Mode.CARETAKER, (task, action) -> {
            if (action == TaskAdapter.TaskAction.START) {
                confirmAction(task.getId(), true);
                return;
            }
            if (action == TaskAdapter.TaskAction.COMPLETE) {
                confirmAction(task.getId(), false);
            }
        }));
    }

    private void confirmAction(long taskId, boolean start) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle(start ? "Start Task" : "Complete Task");
        builder.setMessage(start ? "Mark this task as in progress?" : "Mark this task as completed?");
        builder.setPositiveButton("Yes", (dialog, which) -> updateTask(taskId, start));
        builder.setNegativeButton("Cancel", null);
        builder.show();
    }

    private void updateTask(long taskId, boolean start) {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        Call<TaskActionResponse> call = start
                ? maintenanceRepository.startTask(authorization, taskId)
                : maintenanceRepository.completeTask(authorization, taskId);

        call.enqueue(new Callback<TaskActionResponse>() {
            @Override
            public void onResponse(Call<TaskActionResponse> call, Response<TaskActionResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to update task.");
                    showError(parsedError.getMessage());
                    return;
                }
                Toast.makeText(CaretakerTasksActivity.this, start ? "Task started" : "Task completed", Toast.LENGTH_SHORT).show();
                loadTasks();
            }

            @Override
            public void onFailure(Call<TaskActionResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to update task. Please try again.");
            }
        });
    }

    private String authorizationHeader() {
        String token = sessionManager.getToken();
        return token == null || token.trim().isEmpty() ? null : "Bearer " + token;
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        binding.swipeRefreshLayout.setRefreshing(loading);
    }

    private void showError(String message) {
        binding.cardError.setVisibility(View.VISIBLE);
        binding.tvErrorMessage.setText(message);
    }

    private void hideError() {
        binding.cardError.setVisibility(View.GONE);
    }

}
