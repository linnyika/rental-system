package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class DashboardTaskDto {

    @SerializedName("id")
    private long id;

    @SerializedName("status")
    private String status;

    @SerializedName("description")
    private String description;

    @SerializedName("tenant_name")
    private String tenantName;

    @SerializedName("property_name")
    private String propertyName;

    @SerializedName("unit_number")
    private String unitNumber;

    @SerializedName("created_at")
    private String createdAt;

    public long getId() { return id; }
    public String getStatus() { return status; }
    public String getDescription() { return description; }
    public String getTenantName() { return tenantName; }
    public String getPropertyName() { return propertyName; }
    public String getUnitNumber() { return unitNumber; }
    public String getCreatedAt() { return createdAt; }
}
