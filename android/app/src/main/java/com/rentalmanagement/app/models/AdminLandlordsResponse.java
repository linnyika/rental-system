package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class AdminLandlordsResponse {

    @SerializedName("landlords")
    private List<AdminLandlordItem> landlords;

    public List<AdminLandlordItem> getLandlords() {
        return landlords;
    }
}
