package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.NotificationAdapter;
import com.rentalmanagement.app.databinding.ActivityNotificationsBinding;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.NotificationItem;
import com.rentalmanagement.app.models.NotificationReadResponse;
import com.rentalmanagement.app.models.NotificationsResponse;
import com.rentalmanagement.app.repository.MaintenanceRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class NotificationsActivity extends BaseDashboardActivity {

    private ActivityNotificationsBinding binding;
    private MaintenanceRepository maintenanceRepository;
    private final List<NotificationItem> notifications = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityNotificationsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        maintenanceRepository = new MaintenanceRepository();

        configureToolbar(binding.toolbar, "Notifications", true);

        binding.rvNotifications.setLayoutManager(new LinearLayoutManager(this));
        binding.rvNotifications.setNestedScrollingEnabled(false);

        binding.btnMarkAllRead.setOnClickListener(v -> markAllRead());
        binding.swipeRefreshLayout.setOnRefreshListener(this::loadNotifications);
        binding.btnRetry.setOnClickListener(v -> loadNotifications());

        loadNotifications();
    }

    private void loadNotifications() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.notifications(authorization).enqueue(new Callback<NotificationsResponse>() {
            @Override
            public void onResponse(Call<NotificationsResponse> call, Response<NotificationsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to load notifications.");
                    showError(parsedError.getMessage());
                    return;
                }

                notifications.clear();
                if (response.body().getNotifications() != null) {
                    notifications.addAll(response.body().getNotifications());
                }
                render();
            }

            @Override
            public void onFailure(Call<NotificationsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load notifications. Please try again.");
            }
        });
    }

    private void render() {
        if (notifications.isEmpty()) {
            binding.tvEmpty.setVisibility(View.VISIBLE);
            binding.rvNotifications.setVisibility(View.GONE);
            binding.rvNotifications.setAdapter(new NotificationAdapter(Collections.emptyList(), null));
            return;
        }

        binding.tvEmpty.setVisibility(View.GONE);
        binding.rvNotifications.setVisibility(View.VISIBLE);
        binding.rvNotifications.setAdapter(new NotificationAdapter(notifications, notification -> {
            if (!notification.isRead()) {
                markRead(notification.getId());
            }
        }));
    }

    private void markRead(long notificationId) {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.markNotificationRead(authorization, notificationId).enqueue(new Callback<NotificationReadResponse>() {
            @Override
            public void onResponse(Call<NotificationReadResponse> call, Response<NotificationReadResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to mark notification as read.");
                    showError(parsedError.getMessage());
                    return;
                }
                loadNotifications();
            }

            @Override
            public void onFailure(Call<NotificationReadResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to update notification. Please try again.");
            }
        });
    }

    private void markAllRead() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        maintenanceRepository.markAllNotificationsRead(authorization).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Failed to mark notifications as read.");
                    showError(parsedError.getMessage());
                    return;
                }
                Toast.makeText(NotificationsActivity.this, "All notifications marked as read", Toast.LENGTH_SHORT).show();
                loadNotifications();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to update notifications. Please try again.");
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
