package com.rentalmanagement.app.api;

import com.rentalmanagement.app.models.AddPropertyRequest;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.AuthResponse;
import com.rentalmanagement.app.models.CurrentUserResponse;
import com.rentalmanagement.app.models.LoginRequest;
import com.rentalmanagement.app.models.MaintenanceActionResponse;
import com.rentalmanagement.app.models.MaintenanceCreateRequest;
import com.rentalmanagement.app.models.MaintenanceRequestsResponse;
import com.rentalmanagement.app.models.MaintenanceStatusUpdateRequest;
import com.rentalmanagement.app.models.NotificationReadResponse;
import com.rentalmanagement.app.models.NotificationsResponse;
import com.rentalmanagement.app.models.PropertiesResponse;
import com.rentalmanagement.app.models.RegisterCaretakerRequest;
import com.rentalmanagement.app.models.RegisterTenantRequest;
import com.rentalmanagement.app.models.TaskActionResponse;
import com.rentalmanagement.app.models.TasksResponse;
import com.rentalmanagement.app.models.UnitCreateRequest;
import com.rentalmanagement.app.models.UnitsResponse;
import com.rentalmanagement.app.models.ActivityLogsResponse;
import com.rentalmanagement.app.models.AdminLandlordsResponse;
import com.rentalmanagement.app.models.RegisterLandlordRequest;
import com.rentalmanagement.app.models.UpdateLandlordRequest;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.PATCH;
import retrofit2.http.Path;
import retrofit2.http.POST;
import retrofit2.http.PUT;
public interface ApiService {
    @POST("login")
    Call<AuthResponse> login(@Body LoginRequest request);

    @POST("landlord/signup")
    Call<AuthResponse> landlordSignup(@Body RegisterLandlordRequest request);

    @GET("user")
    Call<CurrentUserResponse> currentUser(@Header("Authorization") String authorization);

    @POST("logout")
    Call<ApiMessageResponse> logout(@Header("Authorization") String authorization);

    @GET("properties")
    Call<PropertiesResponse> properties(@Header("Authorization") String authorization);

        @POST("properties")
        Call<ApiMessageResponse> addProperty(
            @Header("Authorization") String authorization,
            @Body AddPropertyRequest request
        );

        @GET("properties/{property}/units")
        Call<UnitsResponse> units(@Header("Authorization") String authorization, @Path("property") int propertyId);

        @POST("properties/{property}/units")
        Call<ApiMessageResponse> addUnit(
            @Header("Authorization") String authorization,
            @Path("property") int propertyId,
            @Body UnitCreateRequest request
        );

        @GET("available-units")
        Call<UnitsResponse> availableUnits(@Header("Authorization") String authorization);

        @POST("tenants")
        Call<ApiMessageResponse> registerTenant(
            @Header("Authorization") String authorization,
            @Body RegisterTenantRequest request
        );

            @POST("caretakers")
            Call<ApiMessageResponse> registerCaretaker(
                @Header("Authorization") String authorization,
                @Body RegisterCaretakerRequest request
            );

            @GET("admin/landlords")
            Call<AdminLandlordsResponse> adminLandlords(@Header("Authorization") String authorization);

            @PATCH("admin/landlords/{landlord}")
            Call<ApiMessageResponse> updateLandlord(
                @Header("Authorization") String authorization,
                @Path("landlord") long landlordId,
                @Body UpdateLandlordRequest request
            );

            @DELETE("admin/landlords/{landlord}")
            Call<ApiMessageResponse> deleteLandlord(
                @Header("Authorization") String authorization,
                @Path("landlord") long landlordId
            );

            @POST("maintenance-requests")
            Call<MaintenanceActionResponse> submitMaintenanceRequest(
                @Header("Authorization") String authorization,
                @Body MaintenanceCreateRequest request
            );

            @GET("maintenance-requests")
            Call<MaintenanceRequestsResponse> maintenanceRequests(@Header("Authorization") String authorization);

            @GET("tenant/maintenance-requests")
            Call<MaintenanceRequestsResponse> tenantMaintenanceRequests(@Header("Authorization") String authorization);

            @PATCH("maintenance-requests/{maintenanceRequest}/status")
            Call<MaintenanceActionResponse> updateMaintenanceStatus(
                @Header("Authorization") String authorization,
                @Path("maintenanceRequest") long requestId,
                @Body MaintenanceStatusUpdateRequest request
            );

            @GET("caretaker/tasks")
            Call<TasksResponse> caretakerTasks(@Header("Authorization") String authorization);

            @GET("tenant/tasks/completed")
            Call<TasksResponse> tenantCompletedTasks(@Header("Authorization") String authorization);

            @PUT("tasks/{task}/start")
            Call<TaskActionResponse> startTask(
                @Header("Authorization") String authorization,
                @Path("task") long taskId
            );

            @PUT("tasks/{task}/complete")
            Call<TaskActionResponse> completeTask(
                @Header("Authorization") String authorization,
                @Path("task") long taskId
            );

            @PUT("tasks/{task}/confirm")
            Call<ApiMessageResponse> confirmTaskCompletion(
                @Header("Authorization") String authorization,
                @Path("task") long taskId
            );

            @GET("caretaker/activity-logs")
            Call<ActivityLogsResponse> caretakerActivityLogs(@Header("Authorization") String authorization);

            @GET("notifications")
            Call<NotificationsResponse> notifications(@Header("Authorization") String authorization);

            @PUT("notifications/{notification}/read")
            Call<NotificationReadResponse> markNotificationRead(
                @Header("Authorization") String authorization,
                @Path("notification") long notificationId
            );

            @PUT("notifications/read-all")
            Call<ApiMessageResponse> markAllNotificationsRead(@Header("Authorization") String authorization);
}
