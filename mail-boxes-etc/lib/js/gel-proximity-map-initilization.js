var mbeGelSDK = new GelSDK({
    urlEndUser: mbe_gel_proxmity_data.urlEndUser,
    redirectMode: 'POST',
    reference: 'mbegelproximity',
    merchantCode: mbe_gel_proxmity_data.merchantCode ,
    apiKey: mbe_gel_proxmity_data.apiKey ,
    locale: mbe_gel_proxmity_data.locale
})

function openGELProximityModal() {
    mbeGelSDK.createUIModal({
        callbackOk: (data) => {
            let deliveryPointData = {
                "pickupPointId": data.pickupPointId,
                "code": data.code,
                "serviceType": data.serviceType,
                "networkCode": data.networkCode,
                "networkName": data.networkName,
                "courierName": data.courierName,
                "description": data.description,
                "address": data.address,
                "city": data.city,
                "department": data.department,
                "zipCode": data.zipCode,
                "country": data.country,
                "cost": data.cost,
            };
            if(mbe_gel_proxmity_data.debug === true) console.log(data)
            console.log(JSON.stringify(deliveryPointData))
            document.getElementById('gel-proximity-selection').value = JSON.stringify(deliveryPointData)
            document.getElementById('gel-proximity-selection-update').value = JSON.stringify(true)
            document.getElementById('gel-proximity-selection-label').value = deliveryPointData.networkName+ ', ' + deliveryPointData.address + ', ' + deliveryPointData.zipCode +', '+ deliveryPointData.city
            // Fires the checkout section refresh
            jQuery(document).ready(function() {
                jQuery(document.body).trigger('update_checkout');
            })
        },
        callbackKo: () => {
            if(mbe_gel_proxmity_data.debug === true) console.log('error in map callback')
            document.getElementById('gel-proximity-selection').value=''
            document.getElementById('gel-proximity-selection-label').value=''
            jQuery(document).ready(function() {
                jQuery(document.body).trigger('update_checkout');
            })
        }
    })
}