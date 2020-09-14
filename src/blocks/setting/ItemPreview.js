/**
 * ItemPreview
 */
export default function (props) {
	const { attributes, parsedMeta } = props;

	return (
		<>
			<div className='pochipp-block-preview'>
				<div className='pochipp-box'>
					<div className='pochipp-box__image'>
						<img src={parsedMeta.m_image_url} alt='' />
					</div>
					<div className='pochipp-box__body'>
						<div className='pochipp-box__title'>
							{attributes.title}
						</div>
						<div className='pochipp-box__brand'>
							{parsedMeta.brand}
						</div>
						<div className='pochipp-box__price'>
							{parsedMeta.price}
						</div>
					</div>
				</div>
			</div>
			<code
				className='u-mt-20'
				style={{ whiteSpace: 'normal', width: '100%' }}
			>
				{JSON.stringify(parsedMeta)};
			</code>
		</>
	);
}
