package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.os.Handler;
import android.os.Looper;
import android.widget.Toast;
import com.rentalmanagement.app.adapters.DashboardActionAdapter;
import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.adapters.DashboardMetricAdapter;
import android.content.Intent;
import com.rentalmanagement.app.adapters.TaskAdapter;
import com.rentalmanagement.app.databinding.ActivityDashboardBinding;
import com.rentalmanagement.app.models.CaretakerDashboardResponse;
import com.rentalmanagement.app.models.TaskActionResponse;
import com.rentalmanagement.app.models.TaskItem;
import com.rentalmanagement.app.models.TasksResponse;
import com.rentalmanagement.app.R;
import com.rentalmanagement.app.repository.DashboardRepository;
import com.rentalmanagement.app.repository.MaintenanceRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;
import com.rentalmanagement.app.utilities.DashboardMapper;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
public class CaretakerDashboardActivity extends DashboardScreenActivity {

    private DashboardRepository dashboardRepository;
    private MaintenanceRepository maintenanceRepository;
    private final List<TaskItem> tasks = new ArrayList<>();
    private int pendingCalls = 0;
    private final Handler refreshHandler = new Handler(Looper.getMainLooper());
    private final Runnable autoRefreshRunnable = new Runnable() {
        @Override
        public void run() {
            loadDashboard();
            refreshHandler.postDelayed(this, 10000);
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityDashboardBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        dashboardRepository = new DashboardRepository();
        maintenanceRepository = new MaintenanceRepository();
        bindCommonHeader(displayName(), getString(R.string.caretaker_dashboard));
        binding.cardCurrentUnit.setVisibility(View.GONE);
        binding.tvStatsTitle.setText("Task Summary");
        binding.tvStatsTitle.setVisibility(View.VISIBLE);
        binding.rvStats.setVisibility(View.VISIBLE);
        binding.tvActionsTitle.setText("Quick Actions");
        binding.tvActionsTitle.setVisibility(View.VISIBLE);
        binding.rvActions.setVisibility(View.VISIBLE);
        binding.tvPrimaryTitle.setText("Assigned Tasks");
        binding.tvPrimaryTitle.setVisibility(View.VISIBLE);
        binding.rvPrimary.setVisibility(View.VISIBLE);
        binding.tvSecondaryTitle.setText("Activity Logs");
        binding.tvSecondaryTitle.setVisibility(View.VISIBLE);
        binding.rvSecondary.setVisibility(View.VISIBLE);
        setupGridRecycler(binding.rvStats, 1);
        setupGridRecycler(binding.rvActions, 2);
        setupListRecycler(binding.rvPrimary);
        setupListRecycler(binding.rvSecondary);
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadDashboard);
        binding.btnRetry.setOnClickListener(v -> loadDashboard());
        loadDashboard();
    }

    @Override
    protected void onResume() {
        super.onResume();
        refreshHandler.postDelayed(autoRefreshRunnable, 10000);
        loadDashboard();
    }

    @Override
    protected void onPause() {
        super.onPause();
        refreshHandler.removeCallbacks(autoRefreshRunnable);
    }

    private void loadDashboard() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        pendingCalls = 2;
        setLoading(true);
        hideError();

        dashboardRepository.caretakerDashboard(authorization).enqueue(new Callback<CaretakerDashboardResponse>() {
            @Override
            public void onResponse(Call<CaretakerDashboardResponse> call, Response<CaretakerDashboardResponse> response) {
                finishCall();
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load dashboard. Please try again.", v -> loadDashboard());
                    return;
                }
                renderSummary(response.body());
            }

            @Override
            public void onFailure(Call<CaretakerDashboardResponse> call, Throwable t) {
                finishCall();
                showError("Unable to load dashboard. Please try again.", v -> loadDashboard());
            }
        });

        maintenanceRepository.caretakerTasks(authorization).enqueue(new Callback<TasksResponse>() {
            @Override
            public void onResponse(Call<TasksResponse> call, Response<TasksResponse> response) {
                finishCall();
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load assigned tasks.");
                    showError(parsedError.getMessage(), v -> loadDashboard());
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
                finishCall();
                showError("Unable to load assigned tasks. Please try again.", v -> loadDashboard());
            }
        });
    }

    private void renderSummary(CaretakerDashboardResponse response) {
        hideError();
        binding.rvStats.setAdapter(new DashboardMetricAdapter(DashboardMapper.caretakerMetrics(response.getStats())));
        binding.rvActions.setAdapter(new DashboardActionAdapter(DashboardMapper.caretakerActions(), item -> {
            if ("Activity Logs".equalsIgnoreCase(item.getTitle())) {
                openScreen(CaretakerActivityLogsActivity.class);
                return;
            }
            if ("Notifications".equalsIgnoreCase(item.getTitle())) {
                openScreen(NotificationsActivity.class);
            }
        }));
        binding.rvSecondary.setAdapter(new DashboardListAdapter(DashboardMapper.activityLogs(
                response.getActivityLogs() == null ? Collections.emptyList() : response.getActivityLogs()
        )));
    }

    private void renderTasks() {
        if (tasks.isEmpty()) {
            binding.rvPrimary.setAdapter(new TaskAdapter(new ArrayList<>(), TaskAdapter.Mode.CARETAKER, null));
            return;
        }

        binding.rvPrimary.setAdapter(new TaskAdapter(tasks, TaskAdapter.Mode.CARETAKER, (task, action) -> {
            if (action == TaskAdapter.TaskAction.START) {
                updateTask(task.getId(), true);
                return;
            }
            if (action == TaskAdapter.TaskAction.COMPLETE) {
                updateTask(task.getId(), false);
            }
        }));
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
                    showError(parsedError.getMessage(), v -> loadDashboard());
                    return;
                }

                Toast.makeText(CaretakerDashboardActivity.this, start ? "Task started" : "Task completed", Toast.LENGTH_SHORT).show();
                loadDashboard();
            }

            @Override
            public void onFailure(Call<TaskActionResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to update task. Please try again.", v -> loadDashboard());
            }
        });
    }

    private void finishCall() {
        pendingCalls = Math.max(0, pendingCalls - 1);
        if (pendingCalls == 0) {
            setLoading(false);
        }
    }

    private void openScreen(Class<?> targetClass) {
        Intent intent = new Intent(this, targetClass);
        intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
        startActivity(intent);
    }
}
