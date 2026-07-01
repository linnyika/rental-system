package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class UnitCreateRequest {

    @SerializedName("unit_number")
    private final String unitNumber;

    @SerializedName("rent_amount")
    private final double rentAmount;

    public UnitCreateRequest(String unitNumber, double rentAmount) {
        this.unitNumber = unitNumber;
        this.rentAmount = rentAmount;
    }
}
