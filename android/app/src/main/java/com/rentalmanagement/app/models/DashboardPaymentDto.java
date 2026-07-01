package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class DashboardPaymentDto {

    @SerializedName("id")
    private long id;

    @SerializedName("amount")
    private double amount;

    @SerializedName("method")
    private String method;

    @SerializedName("status")
    private String status;

    @SerializedName("payment_date")
    private String paymentDate;

    @SerializedName("tenant_name")
    private String tenantName;

    @SerializedName("property_name")
    private String propertyName;

    @SerializedName("unit_number")
    private String unitNumber;

    public long getId() { return id; }
    public double getAmount() { return amount; }
    public String getMethod() { return method; }
    public String getStatus() { return status; }
    public String getPaymentDate() { return paymentDate; }
    public String getTenantName() { return tenantName; }
    public String getPropertyName() { return propertyName; }
    public String getUnitNumber() { return unitNumber; }
}
