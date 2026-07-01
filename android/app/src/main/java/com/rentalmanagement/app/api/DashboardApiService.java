package com.rentalmanagement.app.api;

import com.rentalmanagement.app.models.AdminDashboardResponse;
import com.rentalmanagement.app.models.CaretakerDashboardResponse;
import com.rentalmanagement.app.models.LandlordDashboardResponse;
import com.rentalmanagement.app.models.TenantDashboardResponse;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;

public interface DashboardApiService {

    @GET("admin/dashboard")
    Call<AdminDashboardResponse> adminDashboard(@Header("Authorization") String authorization);

    @GET("landlord/dashboard")
    Call<LandlordDashboardResponse> landlordDashboard(@Header("Authorization") String authorization);

    @GET("caretaker/dashboard")
    Call<CaretakerDashboardResponse> caretakerDashboard(@Header("Authorization") String authorization);

    @GET("tenant/dashboard")
    Call<TenantDashboardResponse> tenantDashboard(@Header("Authorization") String authorization);
}
