function postAutentiCommerce( orderId ) {
  var confirmed = confirm( "Realmente deseja inicializar essa consulta?" );

  if ( ! confirmed ) return;

  const path = `#post-${orderId} > td.column-autentify_autenti_commerce_status`;
  const elements = document.querySelectorAll(path);
  const orderStatusTd = elements[0];

  var statusTdOldInnerHTML = orderStatusTd.innerHTML;
  updateBeforeIndividual( orderStatusTd );

  jQuery( "#autentify-notice" ).remove();
  jQuery.ajax( {
    url: autentify_ajax.ajax_url,
    type : 'POST',
    data : {
      action : 'autentify_autenti_commerce_initiate_analysis',
      param1 : orderId
    },
    success : function( response ) {
      response = JSON.parse( response );

      var success = response['success'] == true;
      if ( success ) {
        updateAfterPostAutentiCommerceSuccess( orderStatusTd, response['status_html'] );
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-success"><p>' + response['message'] + '</p></div>' );
      } else {
        updateAfterPostAutentiCommerceFail( orderStatusTd, statusTdOldInnerHTML);
        jQuery( '#wpbody-content .wrap ul' )
          .before( '<div id="autentify-notice" class="notice notice-error"><p>' + response['message'] + '</p></div>' );
      }
    },
    error : function( response ) {
      response = JSON.parse( response );

      console.log( response );
    }
  } );
}

function updateBeforeIndividual( orderStatusTd ) {
  orderStatusTd.innerHTML = "Consultando...";
}

function updateAfterPostAutentiCommerceFail( orderStatusTd, statusTdOldInnerHTML ) {
  orderStatusTd.innerHTML = statusTdOldInnerHTML;
}

function updateAfterPostAutentiCommerceSuccess( orderStatusTd, statusHtml ) {
  if (orderStatusTd.innerHTML == statusHtml) {
    return;
  }

  orderStatusTd.innerHTML = statusHtml;
}

document.addEventListener("DOMContentLoaded", function() {
  const path = ".type-shop_order > " +
      "td.autentify_autenti_commerce_status.column-autentify_autenti_commerce_status > " +
      ".autentify-analysis-status";

  const elements = document.querySelectorAll(path);

  elements.forEach(function(element) {
    const orderRow = element.closest(".type-shop_order");
    const orderId = orderRow.getAttribute('id').replace('post-', '');

    if (!allowedToUpdateStatus(this.innerText)) {
      return true;
    }

    jQuery.ajax({
      url: autentify_ajax.ajax_url,
      method: 'POST',
      data: {
        action: 'autentify_autenti_commerce_update_analysis',
        order_id: orderId
      },
      success : function( response ) {
        response = JSON.parse( response );
        const orderStatusTd = element.closest(`td.column-autentify_autenti_commerce_status` );
        updateAfterPostAutentiCommerceSuccess(orderStatusTd, response["status_html"]);
      }
    });
  });
});

function allowedToUpdateStatus(statusText) {
  if (statusText == "Aprovada" || statusText == "Reprovada" || statusText == "Erro") {
    return false;
  }

  return true;
}