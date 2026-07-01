package com.rentalmanagement.app.repository;

import com.rentalmanagement.app.models.ActivityLogsResponse;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.MaintenanceActionResponse;
import com.rentalmanagement.app.models.MaintenanceCreateRequest;
import com.rentalmanagement.app.models.MaintenanceRequestsResponse;
import com.rentalmanagement.app.models.MaintenanceStatusUpdateRequest;
import com.rentalmanagement.app.models.NotificationReadResponse;
import com.rentalmanagement.app.models.NotificationsResponse;
import com.rentalmanagement.app.models.TaskActionResponse;
import com.rentalmanagement.app.models.TasksResponse;

import retrofit2.Call;

public class MaintenanceRepository extends BaseRepository {

    public MaintenanceRepository() {
        super();
    }

    public Call<MaintenanceActionResponse> submitMaintenanceRequest(String authorization, MaintenanceCreateRequest request) {
        return apiService.submitMaintenanceRequest(authorization, request);
    }

    public Call<MaintenanceRequestsResponse> maintenanceRequests(String authorization) {
        return apiService.maintenanceRequests(authorization);
    }

    public Call<MaintenanceRequestsResponse> tenantMaintenanceRequests(String authorization) {
        return apiService.tenantMaintenanceRequests(authorization);
    }

    public Call<MaintenanceActionResponse> updateMaintenanceStatus(String authorization, long requestId, String status) {
        return apiService.updateMaintenanceStatus(authorization, requestId, new MaintenanceStatusUpdateRequest(status));
    }

    public Call<TasksResponse> caretakerTasks(String authorization) {
        return apiService.caretakerTasks(authorization);
    }

    public Call<TasksResponse> tenantCompletedTasks(String authorization) {
        return apiService.tenantCompletedTasks(authorization);
    }

    public Call<TaskActionResponse> startTask(String authorization, long taskId) {
        return apiService.startTask(authorization, taskId);
    }

    public Call<TaskActionResponse> completeTask(String authorization, long taskId) {
        return apiService.completeTask(authorization, taskId);
    }

    public Call<ApiMessageResponse> confirmTaskCompletion(String authorization, long taskId) {
        return apiService.confirmTaskCompletion(authorization, taskId);
    }

    public Call<ActivityLogsResponse> caretakerActivityLogs(String authorization) {
        return apiService.caretakerActivityLogs(authorization);
    }

    public Call<NotificationsResponse> notifications(String authorization) {
        return apiService.notifications(authorization);
    }

    public Call<NotificationReadResponse> markNotificationRead(String authorization, long notificationId) {
        return apiService.markNotificationRead(authorization, notificationId);
    }

    public Call<ApiMessageResponse> markAllNotificationsRead(String authorization) {
        return apiService.markAllNotificationsRead(authorization);
    }
}
